<?php
namespace Enm\TransformerBundle\Tests\Converter;

use Enm\Transformer\BaseTransformerTestClass;
use Enm\Transformer\Enums\ConversionEnum;
use Enm\Transformer\Helpers\EnmConverter;
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



  public function testMaxNestingLevel()
  {
    try
    {
      $object = new ObjectExample();
      $object->setChild(new ObjectExample());
      $object->getChild()->setChild(new ObjectExample());
      $object->getChild()->getChild()->setChild(new ObjectExample());
      $object->getChild()->setA(new ObjectExample());
      $object->getChild()->getA()->setChild(new ObjectExample());

      $converted = $this->getTransformer()->convert($object, 'array', array(), 2);

      $this->assertArrayHasKey('child', $converted);
      $this->assertArrayHasKey('child', $converted['child']);
      $this->assertArrayHasKey('child', $converted['child']['child']);
      $this->assertArrayHasKey('a', $converted['child']);
      $this->assertArrayHasKey('child', $converted['child']['a']);
      $this->assertNull($converted['child']['child']['child']);
      $this->assertNull($converted['child']['a']['child']);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testConverter()
  {
    try
    {
      $object = new ObjectExample();
      $object->setChild(new ObjectExample());
      $object->getChild()->setChild(new ObjectExample());
      $object->getChild()->getChild()->setChild(new ObjectExample());

      $converter = new EnmConverter();

      $value = $converter->convertTo($object, ConversionEnum::ARRAY_CONVERSION);
      $converter->convertTo($value, ConversionEnum::OBJECT_CONVERSION);
      $converter->convertTo($value, ConversionEnum::STRING_CONVERSION);

      $value = $converter->convertTo($object, ConversionEnum::STRING_CONVERSION);
      $converter->convertTo($value, ConversionEnum::STRING_CONVERSION);

      $value = $converter->convertTo($object, ConversionEnum::PUBLIC_OBJECT_CONVERSION);
      $converter->convertTo($value, ConversionEnum::JSON_CONVERSION);

      $value = $converter->convertTo($object, ConversionEnum::JSON_CONVERSION);
      $converter->convertTo($value, ConversionEnum::OBJECT_CONVERSION);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }
}
