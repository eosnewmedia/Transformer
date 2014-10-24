<?php
namespace Enm\TransformerBundle\Tests\Converter;

use Enm\Transformer\BaseTransformerTestClass;
use Enm\Transformer\ObjectExample;

class ConverterTest extends BaseTransformerTestClass
{

  public function testExclude()
  {
    $object       = new \stdClass();
    $object->test = 'Hallo';
    $object->ok   = 'Welt';

    $object->testObject       = new \stdClass();
    $object->testObject->test = 'A';
    $object->testObject->ok   = 'B';

    try
    {
      $array = $this->getTransformer()->convert($object, 'array', array('test', 'testObject' => array('test')));
      $this->assertArrayNotHasKey('test', $array);
      $this->assertArrayNotHasKey('test', $array['testObject']);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testExcludeWithJson()
  {
    $object       = new \stdClass();
    $object->test = 'Hallo';
    $object->ok   = 'Welt';

    $object->testObject       = new \stdClass();
    $object->testObject->test = 'A';
    $object->testObject->ok   = 'B';

    $object = json_encode($object);

    try
    {
      $array = $this->getTransformer()->convert($object, 'array', array('test', 'testObject' => array('test')));
      $this->assertArrayNotHasKey('test', $array);
      $this->assertArrayNotHasKey('test', $array['testObject']);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testExcludeWithClass()
  {
    try
    {
      $object = new ObjectExample(new ObjectExample());

      $array = $this->getTransformer()->convert(
        $object,
        'array',
        array('a', 'child' => array('a', 'child'))
      );

      $this->assertArrayNotHasKey('a', $array);
      $this->assertArrayNotHasKey('a', $array['child']);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testConvertToPublic()
  {
    try
    {
      $object = new ObjectExample(new ObjectExample());

      $std = $this->getTransformer()->convert($object, 'public');

      $this->assertEquals('a', $std->a);
      $this->assertEquals('a', $std->child->a);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }
}
