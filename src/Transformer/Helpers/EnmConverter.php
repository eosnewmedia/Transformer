<?php


namespace Enm\Transformer\Helpers;

use Enm\Transformer\Enums\ConversionEnum;
use Enm\Transformer\Exceptions\TransformerException;

/**
 * Class EnmConverter
 *
 * @package Enm\Transformer\Helpers
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class EnmConverter
{

  /**
   * @param $value
   *
   * @return array
   * @throws TransformerException
   */
  protected function toArray($value)
  {
    switch (gettype($value))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        return $value;
      case ConversionEnum::OBJECT_CONVERSION:
        return $this->objectToArray($value);
      case ConversionEnum::STRING_CONVERSION:
        return $this->jsonToArray($value);
    }
    throw new TransformerException(
      sprintf(
        'Value of type %s can not be converted to array by this method.',
        gettype($value)
      )
    );
  }



  /**
   * @param $value
   *
   * @return object
   * @throws TransformerException
   */
  protected function toObject($value)
  {
    switch (gettype($value))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        return json_decode(json_encode($value));
      case ConversionEnum::OBJECT_CONVERSION:
        return $value;
      case ConversionEnum::STRING_CONVERSION:
        return json_decode(json_encode($this->jsonToArray($value)));
    }
    throw new TransformerException(
      sprintf(
        'Value of type %s can not be converted to object by this method.',
        gettype($value)
      )
    );
  }



  /**
   * @param $value
   *
   * @return string
   * @throws TransformerException
   */
  protected function toString($value)
  {
    switch (gettype($value))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        return implode(', ', $value);
      case ConversionEnum::STRING_CONVERSION:
        return $value;
      case ConversionEnum::OBJECT_CONVERSION:
        $rc = new \ReflectionObject($value);
        if ($rc->hasMethod('__toString'))
        {
          return $rc->getMethod('__toString')->invoke($value);
        }

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
   * @param $value
   *
   * @return string
   * @throws TransformerException
   */
  protected function toJson($value)
  {
    switch (gettype($value))
    {
      case ConversionEnum::ARRAY_CONVERSION:
      case ConversionEnum::STRING_CONVERSION:
        return json_encode($value);
      case ConversionEnum::OBJECT_CONVERSION:
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
   * @param $value
   *
   * @return array
   * @throws TransformerException
   */
  protected function jsonToArray($value)
  {
    try
    {
      return $this->objectToArray(json_decode($value));
    }
    catch (\Exception $e)
    {
      throw new TransformerException("The given Value isn't a valid JSON-String.");
    }
  }



  /**
   * @param $object
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
        $value             = $this->objectToPublicObject($value);
        $returnClass->$key = $value;
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
   * @param $input
   *
   * @return array
   * @throws TransformerException
   */
  protected function objectToArray($input)
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
   * @param $value
   * @param $result_type
   *
   * @return mixed
   * @throws TransformerException
   */
  public function convertTo($value, $result_type)
  {
    switch (strtolower($result_type))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        return $this->toArray($value);
      case ConversionEnum::STRING_CONVERSION:
        return $this->toString($value);
      case ConversionEnum::JSON_CONVERSION:
        return $this->toJson($value);
      case ConversionEnum::OBJECT_CONVERSION:
        return $this->toObject($value);
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
