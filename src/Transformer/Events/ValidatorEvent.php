<?php


namespace Enm\Transformer\Events;

use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\Parameter;

/**
 * Class ValidatorEvent
 *
 * @package Enm\TransformerBundle\Event
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class ValidatorEvent extends TransformerEvent
{


  protected $validator;



  public function __construct(Configuration $configuration, Parameter $parameter, $validator)
  {
    $this->validator = $validator;
    parent::__construct($configuration, $parameter);
  }



  public function getValidator()
  {
    return $this->validator;
  }
}
