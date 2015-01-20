<?php

namespace Enm\TransformerBundle\Tests\Helpers;

use Enm\Transformer\BaseTransformerTestClass;
use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\Parameter;
use Enm\Transformer\Exceptions\TransformerException;
use Enm\Transformer\Helpers\EnmValidator;
use Enm\Transformer\ObjectExample;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Validator\Validation;

class EnmValidatorTest extends BaseTransformerTestClass
{

  /**
   * @return EnmValidator
   */
  protected function getEnmValidator()
  {
    return new EnmValidator(new EventDispatcher(), Validation::createValidator());
  }



  public function testValidateMethod()
  {
    $validator = $this->getEnmValidator();
    try
    {
      $configuration = new Configuration('test');
      $parameter     = new Parameter('test');
      // String
      $configuration->setType('string');
      $parameter->setValue('testValue');
      $validator->validate($configuration, $parameter);
      $this->assertTrue(true);
      // Array
      $configuration->setType('array');
      $parameter->setValue(array('test'));
      $validator->validate($configuration, $parameter);
      $this->assertTrue(true);
      // Boolean
      $configuration->setType('bool');
      $parameter->setValue(true);
      $validator->validate($configuration, $parameter);
      $this->assertTrue(true);
      // Collection
      $configuration->setType('collection');
      $parameter->setValue(array(new ObjectExample(), new ObjectExample()));
      $validator->validate($configuration, $parameter);
      $this->assertTrue(true);
      // Date
      $configuration->setType('date');
      $parameter->setValue('2015-01-20');
      $validator->validate($configuration, $parameter);
      $this->assertTrue(true);
      // Float
      $configuration->setType('float');
      $parameter->setValue(1.2);
      $validator->validate($configuration, $parameter);
      $this->assertTrue(true);
      // Integer
      $configuration->setType('integer');
      $parameter->setValue(1);
      $validator->validate($configuration, $parameter);
      $this->assertTrue(true);
      // Object
      $configuration->setType('object');
      $parameter->setValue(new ObjectExample());
      $validator->validate($configuration, $parameter);
      $this->assertTrue(true);
      // Individual
      $configuration->setType('individual');
      $parameter->setValue('individualTestValue');
      $validator->validate($configuration, $parameter);
      $this->assertTrue(true);
    }
    catch (TransformerException $e)
    {
      $this->fail($e);
    }
  }
}
