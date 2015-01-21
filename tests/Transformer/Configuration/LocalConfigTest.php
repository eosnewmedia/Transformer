<?php


namespace Enm\TransformerBundle\Tests\Configuration;

use Enm\Transformer\BaseTransformerTestClass;
use Enm\Transformer\Exceptions\TransformerException;
use Enm\Transformer\Transformer;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LocalConfigTest extends BaseTransformerTestClass
{

  public function testTransformerWithLocalConfig()
  {
    try
    {
      $config = array(
        'test' => [
          'type' => 'string'
        ]
      );
      $params = array(
        'test' => 'test'
      );

      $object = $this->getTransformer()->transform(new \stdClass(), $config, $params, array('events' => array()));

      $this->assertEquals('test', $object->test);
    }
    catch (TransformerException $e)
    {
      $this->fail($e);
    }
  }



  public function testTransformerWithGlobalConfig()
  {
    try
    {
      $config = array(
        'test' => [
          'type' => 'string'
        ]
      );
      $params = array(
        'test' => 'test'
      );

      $global_config = array('test' => array('events' => array()));

      $transformer = new Transformer(new EventDispatcher(), $global_config);
      $object      = $transformer->transform(new \stdClass(), $config, $params, 'test');

      $this->assertEquals('test', $object->test);
    }
    catch (TransformerException $e)
    {
      $this->fail($e);
    }
  }
}
