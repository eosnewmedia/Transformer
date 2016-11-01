<?php


namespace Enm\Transformer\Events;

use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\Parameter;

/**
 * Class TransformerEvent
 *
 * @package Enm\TransformerBundle\Event
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class TransformerEvent extends ConfigurationEvent
{

  /**
   * @var Parameter
   */
  protected $parameter;



  public function __construct(Configuration $configuration, Parameter $parameter)
  {
    $this->parameter = $parameter;
    parent::__construct($configuration);
  }



  /**
   * @return Parameter
   */
  public function getParameter()
  {
    return $this->parameter;
  }
}
