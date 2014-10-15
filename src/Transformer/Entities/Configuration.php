<?php


namespace Enm\Transformer\Entities;

use Enm\Transformer\Interfaces\ConfigurationOptionInterface;

class Configuration
{

  /**
   * @var string
   */
  protected $key;

  /**
   * @var string
   */
  protected $type = null;

  /**
   * @var string
   */
  protected $renameTo = null;

  /**
   * @var Configuration[]
   */
  protected $children = array();

  /**
   * @var null|Configuration
   */
  protected $parent = null;

  /**
   * @var \Enm\Transformer\Interfaces\ConfigurationOptionInterface
   */
  protected $options = null;

  /**
   * @var array
   */
  protected $events = array();



  public function __construct($key)
  {
    $this->key = $key;
  }



  /**
   * @param string $key
   *
   * @return $this
   */
  public function setKey($key)
  {
    $this->key = $key;

    return $this;
  }



  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }



  /**
   * @param Configuration[] $children
   *
   * @return $this
   */
  public function setChildren(array $children)
  {
    $this->children = $children;

    return $this;
  }



  /**
   * @return Configuration[]
   */
  public function getChildren()
  {
    return $this->children;
  }



  /**
   * @param string $renameTo
   *
   * @return $this
   */
  public function setRenameTo($renameTo)
  {
    $this->renameTo = $renameTo;

    return $this;
  }



  /**
   * @return string
   */
  public function getRenameTo()
  {
    return $this->renameTo;
  }



  /**
   * @param $type
   *
   * @return $this
   */
  public function setType($type)
  {
    $this->type = $type;

    return $this;
  }



  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }



  /**
   * @param array $events
   *
   * @return $this
   */
  public function setEvents(array $events)
  {
    $this->events = $events;

    return $this;
  }



  /**
   * @return array
   */
  public function getEvents()
  {
    return $this->events;
  }



  /**
   * @param Configuration $parent
   *
   * @return $this
   */
  public function setParent(Configuration $parent = null)
  {
    $this->parent = $parent;

    return $this;
  }



  /**
   * @return Configuration|null
   */
  public function getParent()
  {
    return $this->parent;
  }



  /**
   * @return \Enm\Transformer\Interfaces\ConfigurationOptionInterface
   */
  public function getOptions()
  {
    if (!$this->options instanceof ConfigurationOptionInterface)
    {
      $this->options = new ConfigurationOptions();
    }

    return $this->options;
  }



  /**
   * @param ConfigurationOptionInterface $options
   *
   * @return $this
   */
  public function setOptions(ConfigurationOptionInterface $options)
  {
    $this->options = $options;

    return $this;
  }
}
