<?php


namespace Enm\Transformer\Helpers;

use Enm\Transformer\Enums\ConversionEnum;
use Enm\Transformer\Events\ConverterEvent;
use Enm\Transformer\Exceptions\TransformerException;
use Enm\Transformer\TransformerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class EnmConverter
 *
 * @package Enm\Transformer\Helpers
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class EnmConverter
{

  /**
   * @var EventDispatcherInterface
   */
  protected $dispatcher;



  public function __construct(EventDispatcherInterface $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }



  /**
   * @param       $value
   * @param array $exclude
   *
   * @return array
   * @throws TransformerException
   */
  protected function toArray($value, array $exclude)
  {
    switch (gettype($value))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        return $this->excludeFromArray($value, $exclude);
      case ConversionEnum::OBJECT_CONVERSION:
        return $this->excludeFromArray($this->objectToArray($value, $exclude), $exclude);
      case ConversionEnum::STRING_CONVERSION:
        return $this->excludeFromArray($this->objectToArray(json_decode($value)), $exclude);
    }
    throw new TransformerException(
      sprintf(
        'Value of type %s can not be converted to array by this method.',
        gettype($value)
      )
    );
  }



  /**
   * @param array $array
   * @param array $exclude
   *
   * @return array
   */
  protected function excludeFromArray(array $array, array $exclude)
  {
    foreach ($exclude as $key => $value)
    {
      if (is_array($value) && array_key_exists($key, $array))
      {
        $array[$key] = $this->excludeFromArray($array[$key], $value);
      }
      elseif (!is_array($value) && array_key_exists($value, $array))
      {
        /**
         * @var string $value
         */
        unset($array[$value]);
      }
    }

    return $array;
  }



  /**
   * @param object $object
   * @param array  $exclude
   *
   * @return object
   */
  protected function excludeFromObject($object, array $exclude)
  {
    $newObject = clone $object;

    if ($object instanceof \stdClass)
    {
      return $this->excludeFromStdClass($newObject, $exclude);
    }

    $reflection = new \ReflectionObject($newObject);

    foreach ($exclude as $key => $value)
    {
      if (!is_array($value))
      {
        if ($reflection->hasProperty($value))
        {
          $reflectionProperty = $reflection->getProperty($value);
          $reflectionProperty->setAccessible(true);
          $reflectionProperty->setValue($newObject, null);
        }
      }
      elseif ($reflection->hasProperty($key))
      {
        $reflectionProperty = $reflection->getProperty($key);
        $reflectionProperty->setAccessible(true);

        if (is_object($reflectionProperty->getValue($newObject)))
        {
          $reflectionProperty->setValue(
            $newObject,
            $this->excludeFromObject($reflectionProperty->getValue($newObject), $value)
          );
        }
      }
    }

    return $newObject;
  }



  /**
   * @param \stdClass $object
   * @param array     $exclude
   *
   * @return \stdClass
   */
  protected function excludeFromStdClass(\stdClass $object, array $exclude)
  {
    foreach ($exclude as $key => $value)
    {
      if (is_array($value))
      {
        if (property_exists($object, $key))
        {
          $object->$key = $this->excludeFromObject($object->$key, $value);
        }
      }
      else
      {
        if (property_exists($object, $value))
        {
          unset($object->$value);
        }
      }
    }

    return $object;
  }



  /**
   * @param       $value
   * @param array $exclude
   *
   * @return object
   * @throws TransformerException
   */
  protected function toObject($value, array $exclude, $public = false)
  {
    switch (gettype($value))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        return $this->excludeFromObject(json_decode(json_encode($value)), $exclude);
      case ConversionEnum::OBJECT_CONVERSION:
        $value = $this->excludeFromObject($value, $exclude);
        if ($public === false)
        {
          return $value;
        }

        return $this->objectToPublicObject($value);
      case ConversionEnum::STRING_CONVERSION:
        return $this->toObject(json_decode($value), $exclude);
    }
    throw new TransformerException(
      sprintf(
        'Value of type %s can not be converted to object by this method.',
        gettype($value)
      )
    );
  }



  /**
   * @param       $value
   * @param array $exclude
   *
   * @return string
   * @throws TransformerException
   */
  protected function toString($value, array $exclude)
  {
    switch (gettype($value))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        return $this->excludeFromArray(implode(', ', $value), $exclude);
      case ConversionEnum::STRING_CONVERSION:
        return $value;
      case ConversionEnum::OBJECT_CONVERSION:
        $rc = new \ReflectionObject($value);
        if ($rc->hasMethod('__toString'))
        {
          return $rc->getMethod('__toString')->invoke($value);
        }
        $value = $this->excludeFromObject($value, $exclude);

        return json_encode($this->objectToPublicObject($value));
    }
    throw new TransformerException(
      sprintf(
        'Value of type %s can not be converted to String by this method.',
        gettype($value)
      )
    );
  }



  /**
   * @param       $value
   * @param array $exclude
   *
   * @return string
   * @throws TransformerException
   */
  protected function toJson($value, array $exclude)
  {
    switch (gettype($value))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        return json_encode($this->excludeFromArray($value, $exclude));
      case ConversionEnum::STRING_CONVERSION:
        return json_encode($value);
      case ConversionEnum::OBJECT_CONVERSION:
        $value = $this->excludeFromObject($value, $exclude);

        return json_encode($this->objectToPublicObject($value));
    }
    throw new TransformerException(
      sprintf(
        'Value of type %s can not be converted to JSON by this method.',
        gettype($value)
      )
    );
  }



  /**
   * @param       $object
   *
   * @return \stdClass|\DateTime
   */
  protected function objectToPublicObject($object)
  {
    $returnClass = new \stdClass();

    $reflectionObject  = new \ReflectionObject($object);
    $object_properties = $this->objectToArray($object);
    if ($this->shouldBeDateTime($object_properties) === true)
    {
      return new \DateTime($object_properties['date']);
    }

    foreach ($object_properties as $key => $value)
    {
      $property = $reflectionObject->getProperty($key);
      $property->setAccessible(true);
      $value = $property->getValue($object);
      if (is_object($value))
      {
        $returnClass->$key = $this->objectToPublicObject($value);
      }
      elseif (is_array($value))
      {
        $this->prepareArrayForObject($returnClass, $key, $value);
      }
      else
      {
        $returnClass->$key = $value;
      }
    }

    return $returnClass;
  }



  /**
   * @param \stdClass $stdClass
   * @param string    $key
   * @param array     $value
   */
  protected function prepareArrayForObject(\stdClass $stdClass, $key, array $value)
  {
    $array_keys   = array_keys($value);
    $array_values = array_values($value);
    $assoc        = false;
    foreach ($array_keys as $array_key)
    {
      if (!is_numeric($array_key))
      {
        $assoc = true;
        break;
      }
    }
    if ($assoc === true || count($array_values) === 0 || !is_array($array_values[0]))
    {
      $stdClass->$key = $value;
    }
    else
    {
      $collection_array = array();
      foreach ($value as $sub_value)
      {
        array_push($collection_array, $this->objectToPublicObject($sub_value));
      }
      $stdClass->$key = $collection_array;
    }
  }



  /**
   * @param array $value
   *
   * @return bool
   */
  protected function shouldBeDateTime(array $value)
  {
    if (array_key_exists('date', $value))
    {
      if (array_key_exists('timezone_type', $value))
      {
        if (array_key_exists('timezone', $value))
        {
          return true;
        }
      }
    }

    return false;
  }



  /**
   * @param       $input
   * @param array $exclude
   *
   * @return array
   * @throws TransformerException
   */
  protected function objectToArray($input, array $exclude = array())
  {
    if (!in_array(gettype($input), array(ConversionEnum::OBJECT_CONVERSION, ConversionEnum::ARRAY_CONVERSION)))
    {
      throw new TransformerException(
        sprintf(
          "Value of type %s can't be converted by this method!",
          gettype($input)
        )
      );
    }
    // Rückgabe Array erstellen
    $final = array();

    if (is_object($input))
    {
      $input = $this->excludeFromObject($input, $exclude);
    }

    // Object in Array umwandeln
    $array = (array) $input;

    foreach ($array as $key => $value)
    {
      // Protected und Private Properties des Objektes im Array erreichbar machen
      if (is_object($input))
      {
        // PHP Benennungen vom Konvertieren Rückgängigmachen
        $key = str_replace("\0*\0", '', $key);
        $key = str_replace("\0" . get_class($input) . "\0", '', $key);

        // Die Reflection-Klasse wird an dieser Stelle benötigt, da PHP Integer-Werte aus dem Objekt nicht in das Array übernimmts
        $reflectionClass = new \ReflectionClass(get_class($input));
        if ($reflectionClass->hasProperty($key))
        {
          $property = $reflectionClass->getProperty($key);
          $property->setAccessible(true);
          $value = $property->getValue($input);
        }
      }
      // Tiefer verschachtelt?
      if (is_object($value) || is_array($value))
      {
        $value = $this->objectToArray($value);
      }
      // Wert ins Array setzen
      $final[$key] = $value;
    }

    return $final;
  }



  /**
   * @param       $value
   * @param       $result_type
   * @param array $exclude
   *
   * @return mixed
   * @throws TransformerException
   */
  public function convertTo($value, $result_type, array $exclude = array())
  {
    switch (strtolower($result_type))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        $this->dispatcher->dispatch(TransformerEvents::CONVERT_TO_ARRAY, new ConverterEvent($value, $result_type));

        return $this->toArray($value, $exclude);
      case ConversionEnum::STRING_CONVERSION:
        $this->dispatcher->dispatch(TransformerEvents::CONVERT_TO_STRING, new ConverterEvent($value, $result_type));

        return $this->toString($value, $exclude);
      case ConversionEnum::JSON_CONVERSION:
        $this->dispatcher->dispatch(TransformerEvents::CONVERT_TO_JSON, new ConverterEvent($value, $result_type));

        return $this->toJson($value, $exclude);
      case ConversionEnum::OBJECT_CONVERSION:
        $this->dispatcher->dispatch(TransformerEvents::CONVERT_TO_OBJECT, new ConverterEvent($value, $result_type));

        return $this->toObject($value, $exclude, false);
      case ConversionEnum::PUBLIC_OBJECT_CONVERSION:
        $this->dispatcher->dispatch(TransformerEvents::CONVERT_TO_PUBLIC, new ConverterEvent($value, $result_type));

        return $this->toObject($value, $exclude, true);
      default:
        throw new TransformerException(
          sprintf(
            "The given Value can't be converted to %s by this method!",
            gettype($result_type)
          )
        );
    }
  }
}
