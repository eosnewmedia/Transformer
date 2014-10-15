<?php


namespace Enm\Transformer\Helpers;

use Enm\Transformer\Configuration\ObjectConfiguration;
use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\RequireIfOption;
use Enm\Transformer\Enums\TypeEnum;
use Enm\Transformer\Events\ConfigurationEvent;
use Enm\Transformer\Events\ExceptionEvent;
use Enm\Transformer\Exceptions\TransformerException;
use Enm\Transformer\TransformerEvents;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EnmConfigurator
{

  /**
   * @var \Enm\Transformer\Entities\Configuration[]
   */
  protected $configuration;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;



  /**
   * @param array                    $config
   * @param EventDispatcherInterface $eventDispatcher
   * @param Configuration            $parent
   *
   * @throws TransformerException
   */
  public function __construct(array $config, EventDispatcherInterface $eventDispatcher, Configuration $parent = null)
  {
    $this->configuration = array();
    $this->dispatcher    = $eventDispatcher;
    $this->runConfig($config, $parent);
  }



  public function __destruct()
  {
    unset($this->accessor);
    unset($this->configuration);
  }



  /**
   * @return \Enm\Transformer\Entities\Configuration[]
   */
  public function getConfig()
  {
    return $this->configuration;
  }



  /**
   * @param array         $config
   * @param Configuration $parent
   *
   * @throws TransformerException
   */
  protected function runConfig(array $config, Configuration $parent = null)
  {
    try
    {
      $config = $this->validateConfiguration($config);
      foreach ($config as $key => $settings)
      {
        $this->configuration[$key] = new Configuration($key);
        $this->configuration[$key]->setParent($parent);
        $this->setBaseConfiguration($settings, $key);
        $this->setBaseOptions($settings, $key);
        $this->{'set' . ucfirst($this->configuration[$key]->getType()) . 'Options'}($settings, $key);
        $this->createChildren($settings, $key);
        $this->setEventConfiguration($settings, $key);

        $this->dispatcher->dispatch(
          TransformerEvents::AFTER_CONFIGURATION,
          new ConfigurationEvent($this->configuration[$key])
        );
      }

      return $this->configuration;
    }
    catch (\Exception $e)
    {
      $this->dispatcher->dispatch(TransformerEvents::ON_EXCEPTION, new ExceptionEvent($e));
      throw new TransformerException($e->getMessage() . ' --- ' . $e->getFile() . ': ' . $e->getLine());
    }
  }



  /**
   * @param array $config
   *
   * @return array
   */
  protected function validateConfiguration(array $config)
  {
    $processor = new Processor();

    return $processor->processConfiguration(new ObjectConfiguration(), array('config' => $config));
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setEventConfiguration(array $config, $key)
  {
    $this->configuration[$key]->setEvents($config['options']['events']);
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setBaseConfiguration(array $config, $key)
  {
    $this->configuration[$key]->setType(strtolower($config['type']));
    $this->configuration[$key]->setRenameTo($config['renameTo']);
  }



  /**
   * @param array $config
   * @param       $key
   *
   * @throws TransformerException
   */
  protected function createChildren(array $config, $key)
  {
    if (
      in_array($this->configuration[$key]->getType(), array(TypeEnum::COLLECTION_TYPE, TypeEnum::OBJECT_TYPE))
      || ($this->configuration[$key]->getType() === TypeEnum::INDIVIDUAL_TYPE && count($config['children']) > 0)
      || ($this->configuration[$key]->getType() === TypeEnum::ARRAY_TYPE && count($config['children']) > 0)
    )
    {
      $this->configuration[$key]->setChildren($config['children']);
      $this->dispatcher->dispatch(
        TransformerEvents::BEFORE_CHILD_CONFIGURATION,
        new ConfigurationEvent($this->configuration[$key])
      );

      $configuration = new self(
        $this->configuration[$key]->getChildren(),
        $this->dispatcher,
        $this->configuration[$key]
      );

      $this->configuration[$key]->setChildren($configuration->getConfig());

      if (!count($this->configuration[$key]->getChildren()) > 0)
      {
        throw new TransformerException(
          'A child configuration is required for type '
          . $this->configuration[$key]->getType()
        );
      }

      $this->dispatcher->dispatch(
        TransformerEvents::AFTER_CHILD_CONFIGURATION,
        new ConfigurationEvent($this->configuration[$key])
      );
    }
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setBaseOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $options->setRequired($config['options']['required']);
    $options->setDefaultValue($config['options']['defaultValue']);
    $options->setForbiddenIfAvailable(
      array_change_key_case($config['options']['forbiddenIfAvailable'], CASE_LOWER)
    );
    $options->setForbiddenIfNotAvailable(
      array_change_key_case($config['options']['forbiddenIfNotAvailable'], CASE_LOWER)
    );
    $options->setRequiredIfAvailable(
      new RequireIfOption(
        array_change_key_case($config['options']['requiredIfAvailable']['or'], CASE_LOWER),
        array_change_key_case($config['options']['requiredIfAvailable']['and'], CASE_LOWER)
      )
    );
    $options->setRequiredIfNotAvailable(
      new RequireIfOption(
        array_change_key_case($config['options']['requiredIfNotAvailable']['or'], CASE_LOWER),
        array_change_key_case($config['options']['requiredIfNotAvailable']['and'], CASE_LOWER)
      )
    );
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setArrayOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $options->setExpected($config['options']['expected']);
    $options->setAssociative($config['options']['assoc']);
    $options->setRegex($config['options']['regex']);
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setBoolOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $options->setExpected($config['options']['expected']);
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setCollectionOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $options->setReturnClass($config['options']['returnClass']);
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setDateOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $format  = $config['options']['date']['expectedFormat'];
    if (is_array($format))
    {
      $options->setExpectedFormat($format);
    }
    else
    {
      $options->setExpectedFormat(array($format));
    }
    $options->setConvertToObject($config['options']['date']['convertToObject']);
    $options->setConvertToFormat($config['options']['date']['convertToFormat']);
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setFloatOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $options->setExpected($config['options']['expected']);
    $options->setRound($config['options']['round']);
    if (!is_null($config['options']['min']))
    {
      $options->setMin($config['options']['min']);
    }
    if (!is_null($config['options']['max']))
    {
      $options->setMax($config['options']['max']);
    }
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setIntegerOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $options->setExpected($config['options']['expected']);
    if (!is_null($config['options']['min']))
    {
      $options->setMin($config['options']['min']);
    }
    if (!is_null($config['options']['max']))
    {
      $options->setMax($config['options']['max']);
    }
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setIndividualOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $options->setOptions($config['options']['individual']);

    $this->setArrayOptions($config, $key);
    $this->setObjectOptions($config, $key);
    $this->setBoolOptions($config, $key);
    $this->setCollectionOptions($config, $key);
    $this->setDateOptions($config, $key);
    $this->setFloatOptions($config, $key);
    $this->setIntegerOptions($config, $key);
    $this->setStringOptions($config, $key);
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setObjectOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $options->setReturnClass($config['options']['returnClass']);
  }



  /**
   * @param array  $config
   * @param string $key
   */
  protected function setStringOptions(array $config, $key)
  {
    $options = $this->configuration[$key]->getOptions();
    $options->setRegex($config['options']['regex']);
    $options->setValidation(strtolower($config['options']['stringValidation']));
    $options->setStrong($config['options']['strongValidation']);
    $options->setExpected($config['options']['expected']);
    $options->setMax($config['options']['length']['max']);
    $options->setMin($config['options']['length']['min']);
  }
}
