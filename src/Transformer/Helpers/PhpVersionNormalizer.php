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
    if (!function_exists('boolval'))
    {
      $functions = get_defined_functions();
      if (!in_array('boolval', $functions))
      {
        function boolval($var)
        {
          return (bool) $var;
        }
      }
    }
  }
}
