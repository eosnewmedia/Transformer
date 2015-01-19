<?php


namespace Enm\TransformerBundle\Tests\Validator;

use Enm\Transformer\BaseTransformerTestClass;
use Enm\Transformer\Validation\ArrayConstraints\EmptyArrayOrNull;
use Enm\Transformer\Validation\ArrayConstraints\EmptyArrayOrNullValidator;

class ValidatorTest extends BaseTransformerTestClass
{

  public function testEmptyArrayOrNullValidator()
  {
    $value = array();
    try
    {
      $validator = new EmptyArrayOrNullValidator();
      $validator->validate($value, new EmptyArrayOrNull());
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  //  public function testEmptyArrayOrNullValidatorNull()
  //  {
  //    $value = null;
  //    try
  //    {
  //      $validator = new EmptyArrayOrNullValidator();
  //      $validator->validate($value, new EmptyArrayOrNull());
  //      $this->assertTrue(true);
  //    }
  //    catch (\Exception $e)
  //    {
  //      $this->fail($e);
  //    }
  //  }

  //  public function testEmptyArrayOrNullValidatorNotNull()
  //  {
  //    $value = 'abc';
  //    try
  //    {
  //      $validator = new EmptyArrayOrNullValidator();
  //      $validator->validate($value, new EmptyArrayOrNull());
  //      $this->forcedFail();
  //    }
  //    catch (\Exception $e)
  //    {
  //      $this->assertTrue(true);
  //    }
  //  }

  //  public function testEmptyArrayOrNullValidatorNotEmptyArray()
  //  {
  //    $value = array('abc');
  //    try
  //    {
  //      $validator = new EmptyArrayOrNullValidator();
  //      $validator->validate($value, new EmptyArrayOrNull());
  //      $this->forcedFail();
  //    }
  //    catch (\Exception $e)
  //    {
  //      $this->assertTrue(true);
  //    }
  //  }
}
