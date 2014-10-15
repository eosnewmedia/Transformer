<?php


namespace Enm\Transformer\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ClassBuilderEvent
 *
 * @package Enm\Transformer\Events
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class ClassBuilderEvent extends Event
{

  /**
   * @var object|string
   */
  protected $object;



  public function __construct($object)
  {
    $this->object = $object;
  }



  /**
   * @return object|string
   */
  public function getObject()
  {
    return $this->object;
  }



  public function isObject()
  {
    return is_object($this->object);
  }
}
