<?php


namespace Enm\Transformer;

class ObjectExample
{

  private $a = 'a';

  protected $b = 'b';

  public $c = 'c';

  private $child;



  public function __construct(ObjectExample $child = null)
  {
    $this->child = $child;
  }



  /**
   * @return string
   */
  public function getA()
  {
    return $this->a;
  }



  /**
   * @param string $a
   *
   * @return $this
   */
  public function setA($a)
  {
    $this->a = $a;

    return $this;
  }



  /**
   * @return string
   */
  public function getB()
  {
    return $this->b;
  }



  /**
   * @param string $b
   *
   * @return $this
   */
  public function setB($b)
  {
    $this->b = $b;

    return $this;
  }



  /**
   * @return string
   */
  public function getC()
  {
    return $this->c;
  }



  /**
   * @param string $c
   *
   * @return $this
   */
  public function setC($c)
  {
    $this->c = $c;

    return $this;
  }



  /**
   * @return ObjectExample|null
   */
  public function getChild()
  {
    return $this->child;
  }



  /**
   * @param mixed $child
   *
   * @return $this
   */
  public function setChild($child)
  {
    $this->child = $child;

    return $this;
  }
}
