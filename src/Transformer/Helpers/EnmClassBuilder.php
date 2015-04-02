<?php


namespace Enm\Transformer\Helpers;

use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\Parameter;
use Enm\Transformer\Events\ClassBuilderEvent;
use Enm\Transformer\Exceptions\TransformerException;
use Enm\Transformer\TransformerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ClassBuilder
 *
 * @package Enm\Transformer\Helpers
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class EnmClassBuilder
{

  /**
   * @var EventDispatcherInterface
   */
  protected $dispatcher;



  /**
   * @param EventDispatcherInterface $dispatcher
   */
  public function __construct(EventDispatcherInterface $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }



  /**
   * @param object|string   $returnClass
   * @param Configuration[] $config
   * @param Parameter[]     $params
   *
   * @return object
   * @throws TransformerException
   */
  public function build($returnClass, array $config, array $params)
  {
    $returnClass = $this->getObjectInstance($returnClass);
    foreach ($config as $configuration)
    {
      if (!$configuration instanceof Configuration)
      {
        throw new TransformerException(
          'Parameter "config" have to be an array of Configuration-Objects!'
        );
      }
      if (!$params[$configuration->getKey()] instanceof Parameter)
      {
        throw new TransformerException(
          'Parameter "params" have to be an array of Parameter-Objects!'
        );
      }
      $this->setValue($returnClass, $configuration, $params[$configuration->getKey()]);
    }

    return $returnClass;
  }



  /**
   * @param object|string $class
   *
   * @return object
   * @throws TransformerException
   */
  public function getObjectInstance($class)
  {
    // Eventbasierte Objekt-Erzeugung
    $event = new ClassBuilderEvent($class);

    // $class enthält bereits ein Objekt
    if ($event->isObject() === true)
    {
      // Vor der Rückgabe an die EventListener/EventSubscriber geben...
      $this->dispatcher->dispatch(TransformerEvents::OBJECT_RETURN_INSTANCE, $event);

      return $event->getObject();
    }

    // $class enthält noch kein Objekt
    if (class_exists($event->getObject()))
    {
      $this->dispatcher->dispatch(TransformerEvents::OBJECT_CREATE_INSTANCE, $event);

      // Wenn in keinem EventListener/EventSubscriber die Instanz erzeugt wurde...
      if ($event->isObject() === false)
      {
        $reflection = new \ReflectionClass($class);

        return $reflection->newInstanceWithoutConstructor();
      }

      // Wenn in einem EventListener/EventSubscriber die Instanz erzeugt wurde...
      return $event->getObject();
    }
    throw new TransformerException(sprintf('Class %s does not exist.', $class));
  }



  /**
   * @param object        $returnClass
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function setValue($returnClass, Configuration $configuration, Parameter $parameter)
  {
    $key = $configuration->getRenameTo() !== null ? $configuration->getRenameTo() : $configuration->getKey();
    $this->getDefaultValueIfNull($parameter, $returnClass, $key);
    $this->setPropertyValue($returnClass, $key, $parameter->getValue());
  }



  /**
   * @param object $returnClass
   * @param string $key
   *
   * @return \ReflectionProperty
   */
  protected function getPublicProperty($returnClass, $key)
  {
    $reflection = new \ReflectionObject($returnClass);

    $property = $reflection->getProperty($key);
    $property->setAccessible(true);

    return $property;
  }



  /**
   * @param Parameter $parameter
   * @param object    $returnClass
   * @param string    $key
   */
  protected function getDefaultValueIfNull(Parameter $parameter, $returnClass, $key)
  {
    if ($parameter->getValue() === null && (!$returnClass instanceof \stdClass || $returnClass instanceof \DateTime))
    {
      $getter = $this->getGetter($key);

      $value = null;
      if (method_exists($returnClass, $getter))
      {
        $value = $returnClass->{$getter}();
      }
      else
      {
        if (property_exists($returnClass, $key))
        {
          $property = $this->getPublicProperty($returnClass, $key);
          $value    = $property->getValue($returnClass);
        }
      }
      $parameter->setValue($value);
    }
  }



  /**
   * @param object $returnClass
   * @param string $key
   * @param mixed  $value
   *
   * @return bool
   */
  protected function setPropertyValue($returnClass, $key, $value)
  {
    $setter = $this->getSetter($key);
    if ($value !== null && method_exists($returnClass, $setter))
    {
      $returnClass->{$setter}($value);

      return true;
    }
    elseif (!$returnClass instanceof \stdClass || $returnClass instanceof \DateTime)
    {
      if (property_exists($returnClass, $key))
      {
        $property = $this->getPublicProperty($returnClass, $key);
        $property->setValue($returnClass, $value);

        return true;
      }
    }
    $returnClass->$key = $value;

    return true;
  }



  /**
   * @param string $key
   *
   * @return string
   */
  protected function getSetter($key)
  {
    return 'set' . $this->getMethodName($key);
  }



  /**
   * @param string $key
   *
   * @return string
   */
  protected function getGetter($key)
  {
    return 'get' . $this->getMethodName($key);
  }



  /**
   * @param string $key
   *
   * @return string
   */
  protected function getMethodName($key)
  {
    $pieces = explode('_', $key);
    if (count($pieces) > 0)
    {
      $method = '';
      foreach ($pieces as $piece)
      {
        $method .= ucfirst($piece);
      }

      return $method;
    }

    return ucfirst($key);
  }
}
