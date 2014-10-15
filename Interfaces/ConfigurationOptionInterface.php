<?php


namespace Enm\Transformer\Interfaces;

use Enm\Transformer\Entities\RequireIfOption;

/**
 * Interface ConfigurationOptionInterface
 *
 * @package Enm\Transformer\Interfaces
 */
interface ConfigurationOptionInterface
{

  /**
   * @return boolean
   */
  public function isAssociative();



  /**
   * @param boolean $associative
   *
   * @return $this
   */
  public function setAssociative($associative);



  /**
   * @return null|string
   */
  public function getConvertToFormat();



  /**
   * @param string $convert_to_format
   *
   * @return $this
   */
  public function setConvertToFormat($convert_to_format);



  /**
   * @return boolean
   */
  public function isConvertToObject();



  /**
   * @param boolean $convert_to_object
   *
   * @return $this
   */
  public function setConvertToObject($convert_to_object);



  /**
   * @return mixed
   */
  public function getDefaultValue();



  /**
   * @param mixed $default_value
   *
   * @return $this
   */
  public function setDefaultValue($default_value);



  /**
   * @return array
   */
  public function getExpected();



  /**
   * @param array $expected
   *
   * @return $this
   */
  public function setExpected(array $expected);



  /**
   * @return array
   */
  public function getExpectedFormat();



  /**
   * @param array $expectedFormat
   *
   * @return $this
   */
  public function setExpectedFormat(array $expectedFormat);



  /**
   * @return float|int|null
   */
  public function getMax();



  /**
   * @param float|int $max
   *
   * @return $this
   */
  public function setMax($max);



  /**
   * @return float|int|null
   */
  public function getMin();



  /**
   * @param float|int $min
   *
   * @return $this
   */
  public function setMin($min);



  /**
   * @return array
   */
  public function getOptions();



  /**
   * @param array $options
   *
   * @return $this
   */
  public function setOptions(array $options);



  /**
   * @return null|string
   */
  public function getRegex();



  /**
   * @param string $regex
   *
   * @return $this
   */
  public function setRegex($regex);



  /**
   * @return boolean
   */
  public function isRequired();



  /**
   * @param boolean $required
   *
   * @return $this
   */
  public function setRequired($required);



  /**
   * @return RequireIfOption
   */
  public function getRequiredIfAvailable();



  /**
   * @param RequireIfOption $requiredIfAvailable
   *
   * @return $this
   */
  public function setRequiredIfAvailable(RequireIfOption $requiredIfAvailable);



  /**
   * @return RequireIfOption
   */
  public function getRequiredIfNotAvailable();



  /**
   * @param RequireIfOption $requiredIfNotAvailable
   *
   * @return $this
   */
  public function setRequiredIfNotAvailable(RequireIfOption $requiredIfNotAvailable);



  /**
   * @return object|string
   */
  public function getReturnClass();



  /**
   * @param object|string $returnClass
   *
   * @return $this
   */
  public function setReturnClass($returnClass);



  /**
   * @return int|null
   */
  public function getRound();



  /**
   * @param int $round
   *
   * @return $this
   */
  public function setRound($round);



  /**
   * @return boolean
   */
  public function isStrong();



  /**
   * @param boolean $strong
   *
   * @return $this
   */
  public function setStrong($strong);



  /**
   * @return null|string
   */
  public function getValidation();



  /**
   * @param string $validation
   *
   * @return $this
   */
  public function setValidation($validation);



  public function getForbiddenIfAvailable();



  public function getForbiddenIfNotAvailable();



  public function setForbiddenIfAvailable(array $forbiddenIfAvailable);



  public function setForbiddenIfNotAvailable(array $forbiddenIfNotAvailable);
}
