<?php


namespace Enm\Transformer\Helpers;

/**
 * Class EnmPhpNormalizer
 * This Class offers static methods to use php >= 5.5 features (methods) with lower php versions
 *
 * @package Enm\Transformer\Helpers
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class EnmPhpNormalizer
{

  public static function boolval($var)
  {
    if (!function_exists('boolval'))
    {
      return (bool) $var;
    }
    else
    {
      return boolval($var);
    }
  }
}