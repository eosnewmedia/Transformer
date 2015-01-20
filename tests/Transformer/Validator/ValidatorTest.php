<?php


namespace Enm\TransformerBundle\Tests\Validator;

use Enm\Transformer\BaseTransformerTestClass;
use Enm\Transformer\Validation\ArrayConstraints\ArrayRegex;
use Enm\Transformer\Validation\ArrayConstraints\ArrayRegexValidator;
use Enm\Transformer\Validation\ArrayConstraints\EmptyArrayOrNull;
use Enm\Transformer\Validation\ArrayConstraints\EmptyArrayOrNullValidator;
use Enm\Transformer\Validation\ArrayConstraints\NotEmptyArray;
use Enm\Transformer\Validation\ArrayConstraints\NotEmptyArrayValidator;

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



  public function testEmptyArrayOrNullValidatorNull()
  {
    $value = null;
    try
    {
      $validator = new EmptyArrayOrNullValidator();
      $validator->initialize($this->getValidatorExecutionContext());
      $validator->validate($value, new EmptyArrayOrNull());
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testEmptyArrayOrNullValidatorNotNull()
  {
    $value = 'abc';
    try
    {
      $validator = new EmptyArrayOrNullValidator();
      $validator->initialize($this->getValidatorExecutionContext());
      $validator->validate($value, new EmptyArrayOrNull());
      $this->forcedFail();
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testEmptyArrayOrNullValidatorNotEmptyArray()
  {
    $value = array('abc');
    try
    {
      $validator = new EmptyArrayOrNullValidator();
      $validator->initialize($this->getValidatorExecutionContext());
      $validator->validate($value, new EmptyArrayOrNull());
      $this->forcedFail();
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testNotEmptyArrayValidator()
  {
    $value = array('abc');
    try
    {
      $validator = new NotEmptyArrayValidator();
      $validator->initialize($this->getValidatorExecutionContext());
      $validator->validate($value, new NotEmptyArray());
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testNotEmptyArrayValidatorWithEmptyArray()
  {
    $value = array();
    try
    {
      $validator = new NotEmptyArrayValidator();
      $validator->initialize($this->getValidatorExecutionContext());
      $validator->validate($value, new NotEmptyArray());
      $this->forcedFail();
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testArrayRegexValidator()
  {
    $value = array('123');
    try
    {
      $validator = new ArrayRegexValidator();
      $validator->initialize($this->getValidatorExecutionContext());
      $validator->validate($value, new ArrayRegex(array('pattern' => '/d+/')));
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testArrayRegexValidatorInvalidArray()
  {
    $value = array('123', 'abc');
    try
    {
      $validator = new ArrayRegexValidator();
      $validator->initialize($this->getValidatorExecutionContext());
      $validator->validate($value, new ArrayRegex(array('pattern' => '/d+/')));
      $this->forcedFail();
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testArrayRegexValidatorValidString()
  {
    $value = '123';
    try
    {
      $validator = new ArrayRegexValidator();
      $validator->initialize($this->getValidatorExecutionContext());
      $validator->validate($value, new ArrayRegex(array('pattern' => '/d+/')));
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testArrayRegexValidatorInvalidString()
  {
    $value = 'abc';
    try
    {
      $validator = new ArrayRegexValidator();
      $validator->initialize($this->getValidatorExecutionContext());
      $validator->validate($value, new ArrayRegex(array('pattern' => '/d+/')));
      $this->forcedFail();
    }
    catch (\Exception $e)
    {
      $this->assertTrue(true);
    }
  }
}
