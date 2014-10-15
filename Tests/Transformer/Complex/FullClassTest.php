<?php


namespace Enm\TransformerBundle\Tests\Complex;

use Enm\Transformer\Complex\BaseComplex;

class FullClassTest extends BaseComplex
{

  public function testTestCase()
  {
    try
    {
      $this->getTransformer()->transform(new \stdClass(), $this->getConfig(), $this->getParams());
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e->getMessage());
    }
  }
}
