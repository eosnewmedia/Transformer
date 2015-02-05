<?php


namespace Enm\Transformer\Entities;

use Enm\Transformer\Helpers\PhpVersionNormalizer;
use Enm\Transformer\Interfaces\ConfigurationOptionInterface;

class ConfigurationOptions extends PhpVersionNormalizer implements ConfigurationOptionInterface
{

  public function __construct()
  {
    parent::__construct();
  }



  /**
   * @var bool
   */
  protected $associative = true;

  /**
   * @var array
   */
  protected $expected = array();

  /**
   * @var mixed
   */
  protected $default_value = null;

  /**
   * @var null|string
   */
  protected $regex = null;


  /**
   * @var string|object|null
   */
  protected $returnClass = null;


  /**
   * @var array
   */
  protected $expectedFormat = array('Y-m-d', 'd.m.Y');

  /**
   * @var bool
   */
  protected $convert_to_object = false;

  /**
   * @var string|null
   */
  protected $convert_to_format = null;


  /**
   * @var null|float|int
   */
  protected $min = null;

  /**
   * @var null|float|int
   */
  protected $max = null;

  /**
   * @var null|int
   */
  protected $round = null;

  /**
   * @var bool
   */
  protected $floor = false;

  /**
   * @var bool
   */
  protected $ceil = false;

  /**
   * @var array
   */
  protected $options = array();

  /**
   * @var string|null
   */
  protected $validation = null;

  /**
   * @var boolean
   */
  protected $strong = false;

  /**
   * @var boolean
   */
  protected $required = false;

  /**
   * @var RequireIfOption
   */
  protected $requiredIfAvailable;

  /**
   * @var RequireIfOption
   */
  protected $requiredIfNotAvailable;

  /**
   * @var array
   */
  protected $forbiddenIfAvailable = array();

  /**
   * @var array
   */
  protected $forbiddenIfNotAvailable = array();



  /**
   * @return boolean
   */
  public function isAssociative()
  {
    return $this->associative;
  }



  /**
   * @param boolean $associative
   *
   * @return $this
   */
  public function setAssociative($associative)
  {
    $this->associative = boolval($associative);

    return $this;
  }



  /**
   * @return null|string
   */
  public function getConvertToFormat()
  {
    return $this->convert_to_format;
  }



  /**
   * @param string $convert_to_format
   *
   * @return $this
   */
  public function setConvertToFormat($convert_to_format)
  {
    $this->convert_to_format = $convert_to_format;

    return $this;
  }



  /**
   * @return boolean
   */
  public function isConvertToObject()
  {
    return $this->convert_to_object;
  }



  /**
   * @param boolean $convert_to_object
   *
   * @return $this
   */
  public function setConvertToObject($convert_to_object)
  {
    $this->convert_to_object = boolval($convert_to_object);

    return $this;
  }



  /**
   * @return mixed
   */
  public function getDefaultValue()
  {
    return $this->default_value;
  }



  /**
   * @param mixed $default_value
   *
   * @return $this
   */
  public function setDefaultValue($default_value)
  {
    $this->default_value = $default_value;

    return $this;
  }



  /**
   * @return array
   */
  public function getExpected()
  {
    return $this->expected;
  }



  /**
   * @param array $expected
   *
   * @return $this
   */
  public function setExpected(array $expected)
  {
    $this->expected = $expected;

    return $this;
  }



  /**
   * @return array
   */
  public function getExpectedFormat()
  {
    return $this->expectedFormat;
  }



  /**
   * @param array $expectedFormat
   *
   * @return $this
   */
  public function setExpectedFormat(array $expectedFormat)
  {
    $this->expectedFormat = $expectedFormat;

    return $this;
  }



  /**
   * @return float|int|null
   */
  public function getMax()
  {
    return $this->max;
  }



  /**
   * @param float|int $max
   *
   * @return $this
   */
  public function setMax($max)
  {
    if (!is_numeric($max))
    {
      $max = null;
    }
    $this->max = $max;

    return $this;
  }



  /**
   * @return float|int|null
   */
  public function getMin()
  {
    return $this->min;
  }



  /**
   * @param float|int $min
   *
   * @return $this
   */
  public function setMin($min)
  {
    if (!is_numeric($min))
    {
      $min = null;
    }
    $this->min = $min;

    return $this;
  }



  /**
   * @return array
   */
  public function getOptions()
  {
    return $this->options;
  }



  /**
   * @param array $options
   *
   * @return $this
   */
  public function setOptions(array $options)
  {
    $this->options = $options;

    return $this;
  }



  /**
   * @return null|string
   */
  public function getRegex()
  {
    return $this->regex;
  }



  /**
   * @param string $regex
   *
   * @return $this
   */
  public function setRegex($regex)
  {
    $this->regex = $regex;

    return $this;
  }



  /**
   * @return boolean
   */
  public function isRequired()
  {
    return $this->required;
  }



  /**
   * @param boolean $required
   *
   * @return $this
   */
  public function setRequired($required)
  {
    $this->required = boolval($required);

    return $this;
  }



  /**
   * @return RequireIfOption
   */
  public function getRequiredIfAvailable()
  {
    if (!$this->requiredIfAvailable instanceof RequireIfOption)
    {
      $this->requiredIfAvailable = new RequireIfOption();
    }

    return $this->requiredIfAvailable;
  }



  /**
   * @param RequireIfOption $requiredIfAvailable
   *
   * @return $this
   */
  public function setRequiredIfAvailable(RequireIfOption $requiredIfAvailable)
  {
    $this->requiredIfAvailable = $requiredIfAvailable;

    return $this;
  }



  /**
   * @return RequireIfOption
   */
  public function getRequiredIfNotAvailable()
  {
    if (!$this->requiredIfNotAvailable instanceof RequireIfOption)
    {
      $this->requiredIfNotAvailable = new RequireIfOption();
    }

    return $this->requiredIfNotAvailable;
  }



  /**
   * @param RequireIfOption $requiredIfNotAvailable
   *
   * @return $this
   */
  public function setRequiredIfNotAvailable(RequireIfOption $requiredIfNotAvailable)
  {
    $this->requiredIfNotAvailable = $requiredIfNotAvailable;

    return $this;
  }



  /**
   * @return object|string
   */
  public function getReturnClass()
  {
    return $this->returnClass;
  }



  /**
   * @param object|string $returnClass
   *
   * @return $this
   */
  public function setReturnClass($returnClass)
  {
    $this->returnClass = $returnClass;

    return $this;
  }



  /**
   * @return int|null
   */
  public function getRound()
  {
    return $this->round;
  }



  /**
   * @param int $round
   *
   * @return $this
   */
  public function setRound($round)
  {
    if (!is_null($round))
    {
      $round = intval($round);
    }
    $this->round = $round;

    return $this;
  }



  /**
   * @return boolean
   */
  public function isStrong()
  {
    return $this->strong;
  }



  /**
   * @param boolean $strong
   *
   * @return $this
   */
  public function setStrong($strong)
  {
    $this->strong = boolval($strong);

    return $this;
  }



  /**
   * @return null|string
   */
  public function getValidation()
  {
    return $this->validation;
  }



  /**
   * @param string $validation
   *
   * @return $this
   */
  public function setValidation($validation)
  {
    $this->validation = $validation;

    return $this;
  }



  /**
   * @return array
   */
  public function getForbiddenIfAvailable()
  {
    return $this->forbiddenIfAvailable;
  }



  /**
   * @param array $forbiddenIfAvailable
   *
   * @return $this
   */
  public function setForbiddenIfAvailable(array $forbiddenIfAvailable)
  {
    $this->forbiddenIfAvailable = $forbiddenIfAvailable;

    return $this;
  }



  /**
   * @return array
   */
  public function getForbiddenIfNotAvailable()
  {
    return $this->forbiddenIfNotAvailable;
  }



  /**
   * @param array $forbiddenIfNotAvailable
   *
   * @return $this
   */
  public function setForbiddenIfNotAvailable(array $forbiddenIfNotAvailable)
  {
    $this->forbiddenIfNotAvailable = $forbiddenIfNotAvailable;

    return $this;
  }



  /**
   * @return boolean
   */
  public function isCeil()
  {
    return $this->ceil;
  }



  /**
   * @param boolean $ceil
   *
   * @return $this
   */
  public function setCeil($ceil)
  {
    $this->ceil = boolval($ceil);

    return $this;
  }



  /**
   * @return boolean
   */
  public function isFloor()
  {
    return $this->floor;
  }



  /**
   * @param boolean $floor
   *
   * @return $this
   */
  public function setFloor($floor)
  {
    $this->floor = boolval($floor);

    return $this;
  }
}
