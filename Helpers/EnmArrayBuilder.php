<?php

namespace Enm\Transformer\Helpers;

use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\Parameter;
use Enm\Transformer\Exceptions\TransformerException;

/**
 * Class ArrayBuilder
 *
 * @package Enm\Transformer\Helpers
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class EnmArrayBuilder
{

  /**
   * @param Configuration[] $config
   * @param Parameter[]     $params
   *
   * @return array
   * @throws TransformerException
   */
  public function build(array $config, array $params)
  {
    $return = array();
    foreach ($config as $configuration)
    {
      if (!$configuration instanceof Configuration)
      {
        throw new TransformerException(
          'Parameter "config" have to be an array of Configuration-Objects!'
        );
      }
      if (!$params[$configuration->getKey()] instanceof Parameter)
      {
        throw new TransformerException(
          'Parameter "params" have to be an array of Parameter-Objects!'
        );
      }
      $return = $this->setValue($return, $configuration, $params[$configuration->getKey()]);
    }

    return $return;
  }



  /**
   * @param array         $return
   * @param Configuration $configuration
   * @param Parameter     $parameter
   *
   * @return array
   */
  protected function setValue(array $return, Configuration $configuration, Parameter $parameter)
  {
    $key   = $configuration->getRenameTo() !== null ? $configuration->getRenameTo() : $configuration->getKey();
    $array = array($key => $parameter->getValue());

    return array_merge($return, $array);
  }
}
 