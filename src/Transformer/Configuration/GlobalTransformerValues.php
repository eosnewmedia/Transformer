<?php


namespace Enm\Transformer\Configuration;

use Enm\Transformer\Exceptions;

/**
 * Class GlobalTransformerValues
 * Singleton-Pattern
 *
 * @package Enm\Transformer\Configuration
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class GlobalTransformerValues
{

  /**
   * @var self
   */
  protected static $instance = null;

  /**
   * @var array
   */
  protected $param_array;

  /**
   * @var bool
   */
  protected $param_edited = false;

  /**
   * @var \Enm\Transformer\Entities\Configuration[]
   */
  protected $config_array;

  /**
   * @var bool
   */
  protected $config_edited = false;



  private function __construct()
  {
  }



  private function __clone()
  {
  }



  public static function createNewInstance()
  {
    self::$instance = new self();

    return self::$instance;
  }



  /**
   * @return self
   */
  public static function getInstance()
  {
    if (!self::$instance instanceof self)
    {
      self::createNewInstance();
    }

    return self::$instance;
  }



  public static function destroy()
  {
    self::$instance = null;
  }



  /**
   * @return \Enm\Transformer\Entities\Configuration[]
   * @throws Exceptions\TransformerException
   */
  public function getConfig()
  {
    if (!is_array($this->config_array))
    {
      throw new Exceptions\TransformerException('Config-Array is not defined!');
    }

    return $this->config_array;
  }



  /**
   * @param \Enm\Transformer\Entities\Configuration[] $array
   * @param bool                                      $edit
   *
   * @throws Exceptions\TransformerException
   */
  public function setConfig(array $array, $edit = false)
  {
    if (is_array($this->config_array))
    {
      if ($edit === false)
      {
        throw new Exceptions\TransformerException('Config-Array already defined!');
      }
      else
      {
        $this->config_edited = true;
      }
    }
    $this->config_array = $array;
  }



  /**
   * @return \Enm\Transformer\Entities\Parameter[]
   * @throws Exceptions\TransformerException
   */
  public function getParams()
  {
    if (!is_array($this->param_array))
    {
      throw new Exceptions\TransformerException('Param-Array is not defined!');
    }

    return $this->param_array;
  }



  /**
   * @param \Enm\Transformer\Entities\Parameter[] $array
   * @param bool                                  $edit
   *
   * @throws Exceptions\TransformerException
   */
  public function setParams(array $array, $edit = false)
  {
    if (is_array($this->param_array))
    {
      if ($edit === false)
      {
        throw new Exceptions\TransformerException('Param-Array already defined!');
      }
      else
      {
        $this->param_edited = true;
      }
    }
    $this->param_array = $array;
  }



  /**
   * @return boolean
   */
  public function isConfigEdited()
  {
    return $this->config_edited;
  }



  /**
   * @return boolean
   */
  public function isParamEdited()
  {
    return $this->param_edited;
  }
}
