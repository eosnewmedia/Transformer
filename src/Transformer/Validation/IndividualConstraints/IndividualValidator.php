<?php


namespace Enm\Transformer\Validation\IndividualConstraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IndividualValidator extends ConstraintValidator
{

  /**
   * Individual Values won't be checked by any default Validator
   * This Validator is only required to force a Value-Validation without object
   *
   * @param mixed      $value      The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   *
   * @api
   */
  public function validate($value, Constraint $constraint)
  {
  }
}
