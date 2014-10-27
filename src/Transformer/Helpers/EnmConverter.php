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
   * @var array
   */
  protected $objects = array();



  /**
   * @param mixed  $value
   * @param string $to
   * @param array  $exclude
   *
   * @return mixed
   * @throws TransformerException
   */
  public function convertTo($value, $to, array $exclude = array())
  {
    $this->objects = array();
    switch (strtolower($to))
    {
      case ConversionEnum::ARRAY_CONVERSION:
        return $this->toArray($value, $exclude);
        break;
      case ConversionEnum::JSON_CONVERSION:
        return $this->toJSON($value, $exclude);
        break;
      case ConversionEnum::OBJECT_CONVERSION:
        return $this->toObject($value, $exclude);
        break;
      case ConversionEnum::PUBLIC_OBJECT_CONVERSION:
        return $this->toObject($value, $exclude, true);
        break;
      case ConversionEnum::STRING_CONVERSION:
        return $this->toString($value, $exclude);
        break;
    }

    return $this->fail($value, $to);
  }



  /**
   * @param $value
   * @param $to
   *
   * @return bool
   * @throws TransformerException
   */
  protected function fail($value, $to)
  {
    if (true)
    {
      throw new TransformerException(
        sprintf("Value of type %s can not be converted to %s by this method.", gettype($value), $to)
      );
    }

    return false;
  }



  /**
   * @param      $value
   * @param      $exclude
   * @param bool $public
   *
   * @return \stdClass|object
   * @throws TransformerException
   */
  protected function toObject($value, $exclude, $public = false)
  {
    switch (gettype($value))
    {
      case ConversionEnum::OBJECT_CONVERSION:
        if ($public === false)
        {
          return $value;
        }

        return $this->objectToPublicObject($value, $exclude);
        break;
      case ConversionEnum::ARRAY_CONVERSION:
        return $this->arrayToObject($value, $exclude);
        break;
      case ConversionEnum::STRING_CONVERSION:
        return (json_decode($value) === null ? $this->fail($value, 'object') : json_decode($value));
        break;
    }

    return $this->fail($value, 'object');
  }



  protected function toJSON($value, $exclude)
  {
    switch (gettype($value))
    {
      case ConversionEnum::OBJECT_CONVERSION:
        return json_encode($this->objectToPublicObject($value, $exclude));
        break;
      case ConversionEnum::ARRAY_CONVERSION:
        return json_encode($this->prepareArray($value, $exclude));
        break;
      case ConversionEnum::STRING_CONVERSION:

        return (json_decode($value) === null ? json_encode($value) : $value);
        break;
    }

    return $this->fail($value, 'object');
  }



  protected function toArray($value, $exclude)
  {
    switch (gettype($value))
    {
      case ConversionEnum::OBJECT_CONVERSION:
        return $this->objectToArray($value, $exclude);
        break;
      case ConversionEnum::ARRAY_CONVERSION:
        return $this->prepareArray($value, $exclude);
        break;
      case ConversionEnum::STRING_CONVERSION:

        return (json_decode($value) === null ? $this->fail($value, 'array') :
          $this->prepareArray(json_decode($value, true), $exclude));
        break;
    }

    return $this->fail($value, 'array');
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
        return implode(',', $this->prepareArray($value, $exclude));
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



  protected function arrayToObject(array $array, array $exclude)
  {
    $object = new \stdClass();
    foreach ($array as $key => $value)
    {
      if ($this->exclude($key, $exclude) === false)
      {
        $object->$key = $this->prepareArrayValue($value, $this->excludeNext($key, $exclude));
      }
    }

    return $object;
  }



  /**
   * @param $object
   * @param $exclude
   *
   * @return array
   * @throws TransformerException
   */
  protected function objectToArray($object, array $exclude)
  {
    $array = (array) $this->objectToPublicObject($object, $exclude);

    foreach ($array as $key => $value)
    {
      if (is_object($value))
      {
        $value = $this->objectToArray($value, $this->excludeNext($key, $exclude));
      }
      $array[$key] = $value;
    }

    return $array;
  }



  /**
   * @param       $value
   * @param array $exclude
   *
   * @return \stdClass || \DateTime
   * @throws TransformerException
   */
  protected function objectToPublicObject($value, array $exclude = array())
  {
    if (!is_object($value))
    {
      throw new TransformerException(sprintf('Value has to be an object, %s given.', gettype($value)));
    }

    $object_hash = spl_object_hash($value);
    if (!in_array($object_hash, $this->objects))
    {
      if ($value instanceof \DateTime)
      {
        return $value;
      }
      elseif ($value instanceof \stdClass)
      {
        return $this->arrayToObject((array) $value, $exclude);
      }
      else
      {
        return $this->preparePublicObject($value, $exclude);
      }
    }

    return '__parent:' . $object_hash;
  }



  /**
   * @param       $key
   * @param array $exclude
   *
   * @return bool
   */
  protected function exclude($key, array $exclude)
  {
    if (in_array($key, $exclude))
    {
      return true;
    }

    return false;
  }



  /**
   * @param       $key
   * @param array $exclude
   *
   * @return array
   */
  protected function excludeNext($key, array $exclude)
  {
    if (array_key_exists($key, $exclude) && is_array($exclude[$key]))
    {
      return $exclude[$key];
    }

    return array();
  }



  /**
   * @param       $value
   * @param array $exclude
   *
   * @return \stdClass
   * @throws TransformerException
   */
  protected function preparePublicObject($value, array $exclude)
  {
    $reflection = new \ReflectionObject($value);
    $properties = $reflection->getProperties();

    $object = new \stdClass();

    foreach ($properties as $property)
    {
      $key = $property->getName();
      if ($this->exclude($key, $exclude) === false)
      {
        $property->setAccessible(true);
        $object->$key = $property->getValue($value);

        if (is_object($object->$key))
        {
          $object->$key = $this->objectToPublicObject($object->$key, $this->excludeNext($key, $exclude));
        }
        if (is_array($object->$key))
        {
          $object->$key = $this->prepareArray($object->$key, $this->excludeNext($key, $exclude));
        }
      }
    }

    return $object;
  }



  /**
   * @param array $collection
   * @param array $exclude
   *
   * @return array
   * @throws TransformerException
   */
  protected function prepareArray(array $collection, array $exclude)
  {
    foreach ($collection as $key => $value)
    {
      if ($this->exclude($key, $exclude) === false)
      {
        $collection[$key] = $this->prepareArrayValue($value, $this->excludeNext($key, $exclude));
      }
      else
      {
        unset($collection[$key]);
      }
    }

    return $collection;
  }



  /**
   * @param       $value
   * @param array $exclude
   *
   * @return array|\DateTime|\stdClass
   * @throws TransformerException
   */
  protected function prepareArrayValue($value, array $exclude)
  {
    if (is_array($value))
    {
      if (array_key_exists('date', $value)
          && array_key_exists('timezone_type', $value)
          && array_key_exists('timezone', $value)
      )
      {

        return new \DateTime($value['date']);
      }

      return $this->prepareArray($value, $exclude);
    }
    elseif (is_object($value))
    {

      return $this->objectToPublicObject($value, $exclude);
    }

    return $value;
  }
}
