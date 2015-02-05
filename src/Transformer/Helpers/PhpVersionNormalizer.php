<?php


namespace Enm\Transformer\Helpers;

abstract class PhpVersionNormalizer
{

  /**
   * This method register functions from a higher php version to versions under 5.5
   */
  public function __construct()
  {
    $this->addBoolval();
  }



  protected function addBoolval()
  {
    if (!function_exists('boolval') && !function_exists(__NAMESPACE__ . '\boolval'))
    {
      function boolval($var)
      {
        return (bool) $var;
      }
    }
  }
}
