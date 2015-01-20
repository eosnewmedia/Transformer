<?php
namespace Enm\TransformerBundle\Tests\Entities;

use Enm\Transformer\BaseTransformerTestClass;
use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\ConfigurationOptions;

class ConfigurationTest extends BaseTransformerTestClass
{

  public function testConfigurationEntity()
  {
    try
    {
      $configuration = new Configuration('abc');
      $configuration->setKey('test');
      $configuration->setType('string')->setRenameTo('test');
      $config_1 = new Configuration('test_1');
      $config_2 = new Configuration('test_2');
      $configuration->setParent($config_1);
      $configuration->setChildren(array($config_2));
      $configuration->setOptions(new ConfigurationOptions());

      $configuration->setEvents(
        array(
          'listeners'   => [
            'test_event' => [
              'event'    => 'enm.transformer.event.on.exception',
              'class'    => 'TestEvent',
              'method'   => 'test',
              'priority' => 5,
            ]
          ],
          'subscribers' => [
            'test' => 'testSubscriber'
          ],
        )
      );

      $configuration->getKey();
      $configuration->getType();
      $configuration->getRenameTo();
      $configuration->getParent();
      $configuration->getOptions();
      $configuration->getEvents();
      $configuration->getChildren();

      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }
}
