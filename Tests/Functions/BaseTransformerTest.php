<?php


namespace Enm\Transformer\Tests\Functions;

use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\ConfigurationOptions;
use Enm\Transformer\Entities\Parameter;
use Enm\Transformer\Exceptions\TransformerException;
use Enm\Transformer\Helpers\EnmArrayBuilder;
use Enm\Transformer\Helpers\EnmClassBuilder;
use Enm\Transformer\Tests\BaseTransformerTestClass;

class BaseTransformerTest extends BaseTransformerTestClass
{


  protected function getMethod($name)
  {
    $class  = new \ReflectionClass('Enm\Transformer\BaseTransformer');
    $method = $class->getMethod($name);
    $method->setAccessible(true);

    return $method;
  }



  public function testProcess()
  {
    try
    {
      $config = array(
        'user'     => [
          'type'     => 'string',
          'renameTo' => 'username',
          'options'  => [
            'required' => true,
            'expected' => array('testUser'),
            'regex'    => '([a-zA-Z0-9])',
            'length'   => [
              'min' => 3,
              'max' => 12
            ]
          ]
        ],
        'birthday' => [
          'type'    => 'date',
          'options' => [
            'requiredIfAvailable' => [
              'or' => array('user')
            ],
            'date'                => [
              'expectedFormat' => 'Y-m-d'
            ]
          ]
        ],
        'address'  => [
          'type'     => 'collection',
          'renameTo' => 'TEST',
          'options'  => [
            'returnClass' => '\stdClass'
          ],
          'children' => [
            'street' => [
              'type' => 'string',
            ]
          ]
        ]
      );
      $params = array(
        'user'     => 'testUser',
        'birthday' => '1990-01-01',
      );
      $method = $this->getMethod('process');
      $result = $method->invokeArgs($this->getTransformer(), array('\stdClass', $config, $params));
      $this->assertEquals('testUser', $result->username);
      $this->assertEquals('1990-01-01', $result->birthday);
    }
    catch (TransformerException $e)
    {
      $this->fail($e->getMessage());
    }
  }



  public function testInit()
  {
    try
    {
      $method = $this->getMethod('init');
      $method->invoke($this->getTransformer());
      $this->assertTrue(true);
    }
    catch (TransformerException $e)
    {
      $this->fail($e->getMessage());
    }
  }



  public function testPrepareCollection()
  {
    $childrenConfiguration = new Configuration('street');
    $childrenConfiguration->setType('string');
    $childrenConfiguration->setEvents(array('listeners' => array(), 'subscribers' => array()));

    $configuration = new Configuration('address');
    $configuration->setType('collection');
    $configuration->setRenameTo('TEST');
    $options = new ConfigurationOptions();
    $options->setReturnClass('\stdClass');
    $configuration->setOptions($options);
    $configuration->setChildren(array($childrenConfiguration));

    try
    {
      $method = $this->getMethod('prepareCollection');
      $method->invokeArgs($this->getTransformer(), array($configuration, new Parameter('address', array())));

      $method->invokeArgs(
        $this->getTransformer(),
        array($configuration, new Parameter('address', array(['street' => 'bla'])))
      );
      $this->assertTrue(true);
    }
    catch (TransformerException $e)
    {
      $this->fail($e->getMessage());
    }
  }



  public function testDestroy()
  {
    try
    {
      $method = $this->getMethod('destroy');
      $method->invoke($this->getTransformer());
      $this->assertTrue(true);
    }
    catch (TransformerException $e)
    {
      $this->fail($e->getMessage());
    }
  }



  public function testGetArrayBuilder()
  {
    try
    {
      $method = $this->getMethod('getArrayBuilder');

      $this->assertTrue($method->invoke($this->getTransformer()) instanceof EnmArrayBuilder);
    }
    catch (\Exception $e)
    {
      $this->fail($e->getMessage());
    }
  }



  public function testGetClassBuilder()
  {
    try
    {
      $method = $this->getMethod('getClassBuilder');

      $this->assertTrue($method->invoke($this->getTransformer()) instanceof EnmClassBuilder);
    }
    catch (\Exception $e)
    {
      $this->fail($e->getMessage());
    }
  }



  public function testCreateEmptyObjectStructure()
  {
    $config = array(
      'test' => [
        'type' => 'string'
      ]
    );
    try
    {
      $method = $this->getMethod('createEmptyObjectStructure');

      $this->assertTrue(is_object($method->invokeArgs($this->getTransformer(), array($config))));
    }
    catch (\Exception $e)
    {
      $this->fail($e->getMessage());
    }
  }
}
