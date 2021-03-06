<?php
/**
 * EnumInterface.php
 * Date: 13.06.14
 *
 * @author Philipp Marien <marien@eosnewmedia.de>
 */

namespace Enm\Transformer\Interfaces;

/**
 * Interface EnumInterface
 *
 * Can be used together with Enm\Transformer\Traits\EnumTrait
 *
 * @package Enm\Transformer\Interfaces
 */
interface EnumInterface
{

  /**
   * This method returns an array with the static properties of the called class.
   *
   * @return array
   */
  public static function toArray();
}
