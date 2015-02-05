<?php
/**
 * This file registers functions from a higher php version to versions lower than 5.5
 */
namespace {
  if (!function_exists('boolval'))
  {
    function boolval($var)
    {
      return (bool) $var;
    }
  }
}
