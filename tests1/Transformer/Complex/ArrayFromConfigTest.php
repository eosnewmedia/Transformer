<?php


namespace Enm\Transformer\Complex;

use Enm\Transformer\BaseTransformerTestClass;

class ArrayFromConfigTest extends BaseTransformerTestClass
{

  public function testTestCase()
  {
    $config = array(
      'user_id'  => [
        'type' => 'integer'
      ],
      'username' => [
        'type' => 'integer'
      ],
      'password' => [
        'type' => 'integer'
      ]
    );

    $transformer = $this->getTransformer();

    try
    {
      $array = $transformer->getEmptyObjectStructureFromConfig($config, 'array');
      $this->assertArrayHasKey('user_id', $array);
      $this->assertArrayHasKey('username', $array);
      $this->assertArrayHasKey('password', $array);
    }
    catch (\Exception $e)
    {
      $this->fail($e->getMessage());
    }
  }
}
