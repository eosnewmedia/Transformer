<?php


namespace Enm\Transformer\Tests\Entities;

use Enm\Transformer\Entities\Parameter;
use Enm\Transformer\BaseTransformerTestClass;

class ParameterTest extends BaseTransformerTestClass
{

  public function testParameter()
  {
    // Test Constructor
    $parameter = new Parameter('test', 'abc');

    $this->assertTrue($parameter->getKey() === 'test');
    $this->assertTrue($parameter->getValue() === 'abc');

    // Test Setter
    $parameter->setKey('abc');
    $parameter->setValue('test');

    $this->assertTrue($parameter->getKey() === 'abc');
    $this->assertTrue($parameter->getValue() === 'test');
  }
}
 