<?php


namespace Enm\Transformer;

use Enm\Transformer\Configuration\GlobalTransformerValues;
use Enm\Transformer\Configuration\TransformerConfiguration;
use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\Parameter;
use Enm\Transformer\Enums\ConversionEnum;
use Enm\Transformer\Enums\TypeEnum;
use Enm\Transformer\Events\ExceptionEvent;
use Enm\Transformer\Events\TransformerEvent;
use Enm\Transformer\Exceptions\TransformerException;
use Enm\Transformer\Helpers\EnmArrayBuilder;
use Enm\Transformer\Helpers\EnmClassBuilder;
use Enm\Transformer\Helpers\EnmConfigurator;
use Enm\Transformer\Helpers\EnmConverter;
use Enm\Transformer\Helpers\EnmEventHandler;
use Enm\Transformer\Helpers\EnmNormalizer;
use Enm\Transformer\Helpers\EnmValidator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class BaseTransformer
 *
 * @package Enm\Transformer
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
abstract class BaseTransformer
{

  const BUILD_ARRAY = 'array';

  /**
   * @var \Enm\Transformer\Helpers\EnmClassBuilder
   */
  protected $classBuilder;

  /**
   * @var \Enm\Transformer\Helpers\EnmArrayBuilder
   */
  protected $arrayBuilder;

  /**
   * @var \Enm\Transformer\Helpers\EnmValidator
   */
  protected $validator;

  /**
   * @var \Enm\Transformer\Helpers\EnmNormalizer
   */
  protected $normalizer;

  /**
   * @var \Enm\Transformer\Helpers\EnmConverter
   */
  protected $converter;


  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;


  /**
   * @var array
   */
  protected $global_configuration = array();

  /**
   * @var array
   */
  protected $local_configuration = array();


  protected $eventHandler;



  /**
   * @param EventDispatcherInterface $eventDispatcher
   * @param ValidatorInterface       $validator
   * @param array                    $global_config
   */
  public function __construct(
    EventDispatcherInterface $eventDispatcher,
    array $global_config
  )
  {
    $this->dispatcher           = $eventDispatcher;
    $this->global_configuration = $global_config;
    $this->converter            = new EnmConverter();
    $this->normalizer           = new EnmNormalizer($this->converter);
    $this->eventHandler         = new EnmEventHandler($eventDispatcher, $this->getClassBuilder());
    $this->validator            = new EnmValidator($eventDispatcher, Validation::createValidator());
  }



  /**
   * @return EnmArrayBuilder
   */
  public function getArrayBuilder()
  {
    if (!$this->arrayBuilder instanceof EnmArrayBuilder)
    {
      $this->arrayBuilder = new EnmArrayBuilder();
    }

    return $this->arrayBuilder;
  }



  /**
   * @return EnmClassBuilder
   */
  public function getClassBuilder()
  {
    if (!$this->classBuilder instanceof EnmClassBuilder)
    {
      $this->classBuilder = new EnmClassBuilder($this->dispatcher);
    }

    return $this->classBuilder;
  }



  /**
   * @return $this
   */
  protected function init()
  {
    if (array_key_exists('events', $this->local_configuration))
    {
      $this->eventHandler->init($this->local_configuration['events']);
    }

    GlobalTransformerValues::createNewInstance();

    return $this;
  }



  /**
   * @param array $config
   * @param array $params
   */
  protected function setGlobalValues(array $config, array $params)
  {
    $global = GlobalTransformerValues::getInstance();
    $global->setConfig($config);
    $global->setParams($params);

    $this->dispatcher->dispatch(TransformerEvents::AFTER_GLOBAL_VALUES);
  }



  /**
   * @return $this
   */
  protected function destroy()
  {
    if (array_key_exists('events', $this->local_configuration))
    {
      $this->eventHandler->destroy($this->local_configuration['events']);
    }
    $this->local_configuration = array();

    return $this;
  }



  /**
   * @param string|object $returnClass
   * @param mixed         $config
   * @param mixed         $params
   *
   * @return object
   * @throws \Enm\Transformer\Exceptions\TransformerException
   */
  public function process($returnClass, $config, $params)
  {
    try
    {
      $this->init();

      $params       = $this->converter->convertTo($params, ConversionEnum::ARRAY_CONVERSION);
      $config       = $this->converter->convertTo($config, ConversionEnum::ARRAY_CONVERSION);
      $configurator = new EnmConfigurator($config, $this->dispatcher);

      $this->setGlobalValues($configurator->getConfig(), $params);

      $params = GlobalTransformerValues::getInstance()->getParams();

      $returnClass = $this->build($returnClass, $configurator->getConfig(), $params);

      $this->destroy();

      return $returnClass;
    }
    catch (\Exception $e)
    {
      $this->dispatcher->dispatch(TransformerEvents::ON_EXCEPTION, new ExceptionEvent($e));
      $this->destroy();
      throw new TransformerException($e->getMessage());
    }
  }



  /**
   * @param mixed $config
   * @param mixed $object
   *
   * @return object
   * @throws TransformerException
   */
  public function reverseProcess($config, $object)
  {
    try
    {
      $object = $this->process('\stdClass', $config, $object);

      $this->init();
      $configurator = new EnmConfigurator($config, $this->dispatcher);

      $returnClass = $this->reverseBuild(
        '\stdClass',
        $configurator->getConfig(),
        $this->converter->convertTo($object, ConversionEnum::ARRAY_CONVERSION)
      );

      $this->destroy();

      return $returnClass;
    }
    catch (\Exception $e)
    {
      $this->dispatcher->dispatch(TransformerEvents::ON_EXCEPTION, new ExceptionEvent($e));
      throw new TransformerException($e->getMessage());
    }
  }



  /**
   * @param string|object                             $returnClass
   * @param \Enm\Transformer\Entities\Configuration[] $config_array
   * @param array                                     $params
   *
   * @return object|array
   */
  protected function build($returnClass, array $config_array, array $params)
  {
    $params                = array_change_key_case($params, CASE_LOWER);
    $returnClassProperties = array();
    foreach ($config_array as $configuration)
    {
      $parameter = $this->getParameterObject($configuration, $params);

      $this->initRun($configuration, $parameter);

      $this->doRun($configuration, $parameter, $params);

      $returnClassProperties[$configuration->getKey()] = $parameter;

      $this->destroyRun($configuration, $parameter);
    }

    if ($returnClass === self::BUILD_ARRAY)
    {
      return $this->getArrayBuilder()->build($config_array, $returnClassProperties);
    }

    return $this->getClassBuilder()->build($returnClass, $config_array, $returnClassProperties);
  }



  /**
   * @param string|object                             $returnClass
   * @param \Enm\Transformer\Entities\Configuration[] $config_array
   * @param array                                     $params
   *
   * @return object
   */
  protected function reverseBuild($returnClass, array $config_array, array $params)
  {
    $params = array_change_key_case($params, CASE_LOWER);

    $return_properties    = array();
    $return_configuration = array();

    foreach ($config_array as $configuration)
    {
      $parameter = $this->getParameterObject($configuration, $params);
      $this->initRun($configuration, $parameter);

      $modifiedConfiguration = clone $configuration;

      $this->dispatcher->dispatch(
        TransformerEvents::REVERSE_TRANSFORM,
        new TransformerEvent($modifiedConfiguration, $parameter)
      );

      $this->modifyConfiguration($modifiedConfiguration);

      if ($parameter->getValue() !== null)
      {
        switch ($modifiedConfiguration->getType())
        {
          case TypeEnum::COLLECTION_TYPE:
            $this->prepareCollection($modifiedConfiguration, $parameter, true);
            break;
          case TypeEnum::OBJECT_TYPE:
            $this->reverseObject($modifiedConfiguration, $parameter);
            break;
        }
      }

      $return_properties[$modifiedConfiguration->getKey()]    = $parameter;
      $return_configuration[$modifiedConfiguration->getKey()] = $modifiedConfiguration;

      $this->destroyRun($configuration, $parameter);
    }

    return $this->classBuilder->build($returnClass, $return_configuration, $return_properties);
  }



  /**
   * @param Configuration $configuration
   */
  protected function modifyConfiguration(Configuration $configuration)
  {
    if ($configuration->getRenameTo() !== null)
    {
      $rename = $configuration->getRenameTo();
      $configuration->setRenameTo($configuration->getKey());
      $configuration->setKey($rename);
    }
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function initRun(Configuration $configuration, Parameter $parameter)
  {
    $configuration->setEvents($this->setEventConfig($configuration->getEvents()));
    $this->eventHandler->init($configuration->getEvents());

    $this->dispatcher->dispatch(
      TransformerEvents::BEFORE_RUN,
      new TransformerEvent($configuration, $parameter)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   * @param array         $params
   */
  protected function doRun(Configuration $configuration, Parameter $parameter, array $params)
  {
    $this->validator->requireValue($configuration, $parameter, $params);

    $this->validator->forbidValue($configuration, $parameter, $params);

    if (!is_null($parameter->getValue()))
    {
      $this->validator->validate($configuration, $parameter);

      $this->prepareValue($configuration, $parameter);
    }
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function destroyRun(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::AFTER_RUN,
      new TransformerEvent($configuration, $parameter)
    );

    $this->eventHandler->destroy($configuration->getEvents());
  }



  /**
   * @param Configuration $configuration
   * @param array         $params
   *
   * @return Parameter
   */
  protected function getParameterObject(Configuration $configuration, array $params)
  {
    $parameter = new Parameter($configuration->getKey(), $this->getValue($configuration, $params));
    $this->setDefaultIfNull($configuration, $parameter);
    $this->normalizer->normalize($parameter, $configuration);

    return $parameter;
  }



  /**
   * @param Configuration $configuration
   * @param array         $params
   *
   * @return mixed
   */
  protected function getValue(Configuration $configuration, array $params)
  {
    if (array_key_exists(strtolower($configuration->getKey()), $params))
    {
      return $params[strtolower($configuration->getKey())];
    }
    if (array_key_exists(strtolower($configuration->getRenameTo()), $params))
    {
      return $params[strtolower($configuration->getRenameTo())];
    }

    return null;
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   *
   * @return $this
   */
  protected function prepareValue(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::PREPARE_VALUE,
      new TransformerEvent($configuration, $parameter)
    );

    switch ($configuration->getType())
    {
      case TypeEnum::COLLECTION_TYPE:
        $this->prepareCollection($configuration, $parameter);
        break;
      case TypeEnum::OBJECT_TYPE:
        $this->prepareObject($configuration, $parameter);
        break;
      case TypeEnum::DATE_TYPE:
        $this->prepareDate($configuration, $parameter);
        break;
      case TypeEnum::INDIVIDUAL_TYPE:
        $this->prepareIndividual($configuration, $parameter);
        break;
      case TypeEnum::ARRAY_TYPE:
        $this->prepareArray($configuration, $parameter);
        break;
    }

    return $this;
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   *
   * @return $this
   */
  protected function setDefaultIfNull(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::PREPARE_DEFAULT,
      new TransformerEvent($configuration, $parameter)
    );

    if ($parameter->getValue() === null)
    {
      $value = $configuration->getOptions()->getDefaultValue();
      $parameter->setValue($value);
    }

    return $this;
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   * @param bool          $reverse
   *
   * @return $this
   */
  protected function prepareCollection(Configuration $configuration, Parameter $parameter, $reverse = false)
  {
    if ($reverse === false)
    {
      $this->dispatcher->dispatch(
        TransformerEvents::PREPARE_COLLECTION,
        new TransformerEvent($configuration, $parameter)
      );
    }
    else
    {
      $this->dispatcher->dispatch(
        TransformerEvents::REVERSE_COLLECTION,
        new TransformerEvent($configuration, $parameter)
      );
    }

    $child_array = $parameter->getValue();

    $object_configuration = clone $configuration;
    $object_configuration->setType(TypeEnum::OBJECT_TYPE);
    $object_configuration->getOptions()->setReturnClass(
      $configuration->getOptions()->getReturnClass()
    );
    $object_configuration->getOptions()->setDefaultValue(
      $configuration->getOptions()->getDefaultValue()
    );
    $object_configuration->setParent($configuration);

    $result_array = array();
    $childs       = count($child_array);
    for ($i = 0; $i < $childs; ++$i)
    {
      $param = new Parameter($i, $child_array[$i]);
      if ($reverse === false)
      {
        $this->prepareObject($object_configuration, $param);
      }
      else
      {
        $this->reverseObject($object_configuration, $param);
      }

      array_push($result_array, $param->getValue());
    }

    $parameter->setValue($result_array);

    return $this;
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   *
   * @return $this
   */
  protected function prepareObject(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::PREPARE_OBJECT,
      new TransformerEvent($configuration, $parameter)
    );

    $value = $this->build(
      $configuration->getOptions()->getReturnClass(),
      $configuration->getChildren(),
      $this->converter->convertTo($parameter->getValue(), 'array')
    );
    $parameter->setValue($value);

    return $this;
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   *
   * @return $this
   */
  protected function prepareArray(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::PREPARE_ARRAY,
      new TransformerEvent($configuration, $parameter)
    );

    $return = self::BUILD_ARRAY;

    if (count($configuration->getChildren()) > 0
        && $configuration->getOptions()->isAssociative() === true
    )
    {
      $value = $this->build(
        $return,
        $configuration->getChildren(),
        $this->converter->convertTo($parameter->getValue(), 'array')
      );
      $parameter->setValue($value);
    }

    return $this;
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   *
   * @return $this
   */
  protected function reverseObject(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::REVERSE_OBJECT,
      new TransformerEvent($configuration, $parameter)
    );

    $returnClass = '\stdClass';
    $value       = $this->reverseBuild(
      $returnClass,
      $configuration->getChildren(),
      $this->converter->convertTo($parameter->getValue(), 'array')
    );
    $parameter->setValue($value);

    return $this;
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   *
   * @return $this
   */
  protected function prepareDate(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::PREPARE_DATE,
      new TransformerEvent($configuration, $parameter)
    );

    $date = new \DateTime();
    foreach ($configuration->getOptions()->getExpectedFormat() as $format)
    {
      $date = \DateTime::createFromFormat($format, $parameter->getValue());
      if ($date instanceof \DateTime)
      {
        break;
      }
    }
    if ($configuration->getOptions()->isConvertToObject() === true)
    {
      $parameter->setValue($date);
    }
    elseif ($configuration->getOptions()->getConvertToFormat() !== null)
    {
      $parameter->setValue($date->format($configuration->getOptions()->getConvertToFormat()));
    }

    return $this;
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   *
   * @return $this
   */
  protected function prepareIndividual(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::PREPARE_INDIVIDUAL,
      new TransformerEvent($configuration, $parameter)
    );

    return $this;
  }



  /**
   * @param mixed $local_config
   *
   * @return $this
   * @throws \Enm\Transformer\Exceptions\TransformerException
   */
  protected function setLocalConfig($local_config = null)
  {
    if (is_null($local_config))
    {
      $this->local_configuration = array();

      return $this;
    }
    try
    {
      $processor = new Processor();
      $key       = md5(time());
      $config    = array();

      switch (gettype($local_config))
      {
        case ConversionEnum::STRING_CONVERSION:
          if (!json_decode($local_config) instanceof \stdClass)
          {
            if (!array_key_exists($local_config, $this->global_configuration))
            {
              throw new TransformerException('Config Key "' . $local_config . '" does not exists!');
            }
            $config = $this->global_configuration[$local_config];
            $key    = $local_config;
          }
          break;
        case ConversionEnum::ARRAY_CONVERSION:
          $config = $local_config;
          break;
        default:
          $config = $this->converter->convertTo($local_config, 'array');
      }
      $config = $processor->processConfiguration(
        new TransformerConfiguration(),
        array('enm_transformer' => [$key => $config])
      );

      $this->local_configuration = $config[$key];

      $this->local_configuration['events'] = $this->setEventConfig($this->local_configuration['events']);

      return $this;
    }
    catch (\Exception $e)
    {
      $this->dispatcher->dispatch(TransformerEvents::ON_EXCEPTION, new ExceptionEvent($e));
      throw new TransformerException($e->getMessage());
    }
  }



  /**
   * @param array $event_config
   *
   * @return array
   */
  protected function setEventConfig(array $event_config)
  {
    foreach ($event_config['listeners'] as $key => $listener)
    {
      $event_config['listeners'][$key]['class'] = $this->classBuilder->getObjectInstance($listener['class']);
    }

    return $event_config;
  }



  /**
   * @param $config
   *
   * @return object
   * @throws \Enm\Transformer\Exceptions\TransformerException
   */
  protected function createEmptyObjectStructure($config)
  {
    try
    {
      $configurator = new EnmConfigurator($this->converter->convertTo($config, 'array'), $this->dispatcher);

      return $this->createPartOfStructure($configurator->getConfig());
    }
    catch (\Exception $e)
    {
      $this->dispatcher->dispatch(TransformerEvents::ON_EXCEPTION, new ExceptionEvent($e));
      throw new TransformerException($e->getMessage());
    }
  }



  /**
   * @param \Enm\Transformer\Entities\Configuration[] $config
   *
   * @return object
   */
  protected function createPartOfStructure(array $config)
  {
    $return_properties    = array();
    $return_configuration = array();

    foreach ($config as $configuration)
    {
      $value = null;
      switch ($configuration->getType())
      {
        case TypeEnum::COLLECTION_TYPE:
          $value = array($this->createPartOfStructure($configuration->getChildren()));
          break;
        case TypeEnum::OBJECT_TYPE:
          $value = $this->createPartOfStructure($configuration->getChildren());
          break;
        case TypeEnum::ARRAY_TYPE:
          $value = array();
          break;
        default:
          break;
      }
      $this->modifyConfiguration($configuration);
      $parameter = new Parameter($configuration->getKey(), $value);

      $return_properties[$configuration->getKey()]    = $parameter;
      $return_configuration[$configuration->getKey()] = $configuration;
    }

    return $this->classBuilder->build('\stdClass', $return_configuration, $return_properties);
  }
}
