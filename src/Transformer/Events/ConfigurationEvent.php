<?php


namespace Enm\Transformer\Events;

use Enm\Transformer\Entities\Configuration;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConfigurationEvent
 *
 * @package Enm\Transformer\Events
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class ConfigurationEvent extends Event
{


  /**
   * @var Configuration
   */
  protected $configuration;



  /**
   * @param Configuration $configuration
   */
  public function __construct(Configuration $configuration)
  {
    $this->configuration = $configuration;
  }



  /**
   * @return Configuration
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }
}
