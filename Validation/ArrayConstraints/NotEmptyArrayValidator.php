<?php


namespace Enm\Transformer\Validation\ArrayConstraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotEmptyArrayValidator extends ConstraintValidator
{

  /**
   * @param mixed         $value
   * @param NotEmptyArray $constraint
   */
  public function validate($value, Constraint $constraint)
  {
    if (is_array($value) && count($value) === 0)
    {
      $this->context->addViolation($constraint->message);
    }
  }
}
