<?php
namespace Enm\TransformerBundle\Tests\Configuration;

use Enm\Transformer\BaseTransformerTestClass;
use Enm\Transformer\Configuration\GlobalTransformerValues;
use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\Parameter;

class GlobalTransformerValuesTest extends BaseTransformerTestClass
{

  public function testGlobalTransformerValues()
  {
    try
    {
      GlobalTransformerValues::destroy();
      $global = GlobalTransformerValues::getInstance();
      $global->setConfig(array(new Configuration('test')), false);
      $global->setConfig(array(new Configuration('test')), true);
      $global->setParams(array(new Parameter('test', 'test')), false);
      $global->setParams(array(new Parameter('test', 'test')), true);
      $global->getParams();
      $global->getConfig();
      $global->isConfigEdited();
      $global->isParamEdited();

      GlobalTransformerValues::createNewInstance();

      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testGlobalTransformerValuesNoConfig()
  {
    try
    {
      $global = GlobalTransformerValues::createNewInstance();
      $global->getConfig();

      $this->forcedFail();
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testGlobalTransformerValuesNoParams()
  {
    try
    {
      $global = GlobalTransformerValues::createNewInstance();
      $global->getParams();

      $this->forcedFail();
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testGlobalTransformerValuesWrongConfig()
  {
    try
    {
      $global = GlobalTransformerValues::createNewInstance();
      $global->setConfig(array());
      $global->setConfig(array());

      $this->forcedFail();
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testGlobalTransformerValuesWrongParams()
  {
    try
    {
      $global = GlobalTransformerValues::createNewInstance();
      $global->setParams(array());
      $global->setParams(array());

      $this->forcedFail();
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testGlobalTransformerValuesClone()
  {
    try
    {
      $global     = GlobalTransformerValues::createNewInstance();
      $reflection = new \ReflectionClass($global);
      $method     = $reflection->getMethod('__clone');
      $method->setAccessible(true);
      $method->invoke($global);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }
}
