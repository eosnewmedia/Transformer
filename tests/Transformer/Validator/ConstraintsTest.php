<?php

namespace Enm\TransformerBundle\Tests\Validator;

use Enm\Transformer\BaseTransformerTestClass;
use Enm\Transformer\Validation\ArrayConstraints\ArrayRegex;
use Enm\Transformer\Validation\ArrayConstraints\EmptyArrayOrNull;
use Enm\Transformer\Validation\ArrayConstraints\NotEmptyArray;
use Enm\Transformer\Validation\DateConstraints\Date;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class ConstraintsTest extends BaseTransformerTestClass
{

  public function testDateConstraint()
  {
    try
    {
      new Date(array('format' => array('Y-m-d')));
      $this->assertTrue(true);
    }
    catch (ConstraintDefinitionException $e)
    {
      $this->fail($e);
    }
  }



  public function testDateConstraintNoOptions()
  {
    try
    {
      new Date();
      $this->forcedFail();
    }
    catch (MissingOptionsException $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testDateConstraintNoFormatOption()
  {
    try
    {
      new Date(array());
      $this->forcedFail();
    }
    catch (ConstraintDefinitionException $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testDateConstraintWrongFormatOption()
  {
    try
    {
      new Date(array('format' => 'Y-m-d'));
      $this->forcedFail();
    }
    catch (ConstraintDefinitionException $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testEmptyArrayOrNullConstraint()
  {
    try
    {
      new EmptyArrayOrNull();
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testNotEmptyArrayConstraint()
  {
    try
    {
      new NotEmptyArray();
      $this->assertTrue(true);
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testArrayRegexConstraint()
  {
    try
    {
      $arrayRegex = new ArrayRegex(array('pattern' => '/d+/'));
      $this->assertTrue(true);
      $this->assertEquals('pattern', $arrayRegex->getDefaultOption());
      $this->assertContains('pattern', $arrayRegex->getRequiredOptions());
    }
    catch (\Exception $e)
    {
      $this->fail($e);
    }
  }



  public function testArrayRegexConstraintNoOptions()
  {
    try
    {
      new ArrayRegex();
      $this->forcedFail();
    }
    catch (MissingOptionsException $e)
    {
      $this->assertTrue(true);
    }
  }



  public function testArrayRegexConstraintNoPatternOption()
  {
    try
    {
      new ArrayRegex(array());
      $this->forcedFail();
    }
    catch (MissingOptionsException $e)
    {
      $this->assertTrue(true);
    }
  }
}
