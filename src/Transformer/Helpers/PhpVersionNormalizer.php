<?php


namespace Enm\Transformer\Helpers;

abstract class PhpVersionNormalizer
{

  /**
   * This method register functions from a higher php version to versions under 5.5
   */
  public function __construct()
  {
    if (!function_exists('boolval'))
    {
      try
      {
        function boolval($var)
        {
          return (bool) $var;
        }
      }
      catch (\Exception $e)
      {
      }
    }
  }
}
