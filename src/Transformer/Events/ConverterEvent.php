<?php


namespace Enm\Transformer\Events;

use Symfony\Component\EventDispatcher\Event;

class ConverterEvent extends Event
{

  /**
   * @var string
   */
  protected $convert_to;

  /**
   * @var mixed
   */
  protected $value;



  function __construct($value, $convert_to)
  {
    $this->convert_to = $convert_to;
    $this->value      = $value;
  }



  /**
   * @return string
   */
  public function getConvertTo()
  {
    return $this->convert_to;
  }



  /**
   * @param string $convert_to
   *
   * @return $this
   */
  public function setConvertTo($convert_to)
  {
    $this->convert_to = $convert_to;

    return $this;
  }



  /**
   * @return mixed
   */
  public function getValue()
  {
    return $this->value;
  }



  /**
   * @param mixed $value
   *
   * @return $this
   */
  public function setValue($value)
  {
    $this->value = $value;

    return $this;
  }
}
 