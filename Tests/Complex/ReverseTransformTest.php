<?php


namespace Enm\Transformer\Tests\Complex;

class ReverseTransformTest extends BaseComplex
{

  public function testReverse()
  {

    try
    {
      $object = $this->getTransformer()->transform(new \stdClass(), $this->getConfig(), $this->getParams());
      $this->assertArrayHasKey(
        'user',
        $this->getTransformer()->reverseTransform($object, $this->getConfig(), null, 'array')
      );
      $this->assertObjectHasAttribute(
        'user',
        $this->getTransformer()->reverseTransform($object, $this->getConfig(), null, 'object')
      );
      $this->assertJson($this->getTransformer()->reverseTransform($object, $this->getConfig(), null, 'string'));
      $this->assertJson($this->getTransformer()->reverseTransform($object, $this->getConfig(), null, 'json'));

      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e->getFile() . ' - ' . $e->getLine() . ': ' . $e->getMessage());
    }
  }
}
