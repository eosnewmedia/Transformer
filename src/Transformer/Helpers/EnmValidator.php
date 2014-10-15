<?php


namespace Enm\Transformer\Helpers;

use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\Parameter;
use Enm\Transformer\Enums\StringValidationEnum;
use Enm\Transformer\Enums\TypeEnum;
use Enm\Transformer\Events\ValidatorEvent;
use Enm\Transformer\TransformerEvents;
use Enm\Transformer\Validation\ArrayConstraints\ArrayRegex;
use Enm\Transformer\Validation\ArrayConstraints\EmptyArrayOrNull;
use Enm\Transformer\Validation\ArrayConstraints\NotEmptyArray;
use Enm\Transformer\Validation\DateConstraints\Date;
use Symfony\Component\Validator\Constraints;

class EnmValidator extends EnmBaseValidator
{

  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  public function validate(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::BEFORE_VALIDATION,
      new ValidatorEvent($configuration, $parameter, $this)
    );
    switch ($configuration->getType())
    {
      case TypeEnum::ARRAY_TYPE:
        $this->validateArray($configuration, $parameter);
        break;
      case TypeEnum::BOOL_TYPE:
        $this->validateBool($configuration, $parameter);
        break;
      case TypeEnum::COLLECTION_TYPE:
        $this->validateCollection($configuration, $parameter);
        break;
      case TypeEnum::DATE_TYPE:
        $this->validateDate($configuration, $parameter);
        break;
      case TypeEnum::FLOAT_TYPE:
        $this->validateFloat($configuration, $parameter);
        break;
      case TypeEnum::INTEGER_TYPE:
        $this->validateInteger($configuration, $parameter);
        break;
      case TypeEnum::OBJECT_TYPE:
        $this->validateObject($configuration, $parameter);
        break;
      case TypeEnum::STRING_TYPE:
        $this->validateString($configuration, $parameter);
        break;
      case TypeEnum::INDIVIDUAL_TYPE:
        $this->validateIndividual($configuration, $parameter);
        break;
    }
    $this->validateConstrains($this->getConstraints(), $parameter);
    $this->clearConstraints();
    $this->dispatcher->dispatch(
      TransformerEvents::AFTER_VALIDATION,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   * @param array         $params
   */
  public function requireValue(Configuration $configuration, Parameter $parameter, array $params)
  {
    $constraints = array();

    if ($configuration->getOptions()->isRequired() === false)
    {
      $methods = array(
        'requireIfAvailableAnd',
        'requireIfAvailableOr',
        'requireIfNotAvailableAnd',
        'requireIfNotAvailableOr'
      );

      $i = 0;

      while ($configuration->getOptions()->isRequired() === false && $i < count($methods))
      {
        $configuration->getOptions()->setRequired($this->{$methods[$i]}($configuration, $params));
        ++$i;
      }
    }
    if ($configuration->getOptions()->isRequired() === true)
    {
      array_push($constraints, new Constraints\NotNull());
      if (in_array($configuration->getType(), array(TypeEnum::COLLECTION_TYPE, TypeEnum::OBJECT_TYPE)))
      {
        array_push($constraints, new NotEmptyArray());
      }
      $this->validateConstrains($constraints, $parameter);
    }
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   * @param array         $params
   */
  public function forbidValue(Configuration $configuration, Parameter $parameter, array $params)
  {
    $constraints = array();

    $forbidden = false;

    $ifAvailable    = $configuration->getOptions()->getForbiddenIfAvailable();
    $ifNotAvailable = $configuration->getOptions()->getForbiddenIfNotAvailable();

    if ($forbidden !== true)
    {
      foreach ($ifAvailable as $key)
      {
        if (array_key_exists(strtolower($key), $params))
        {
          $forbidden = true;
          break;
        }
      }
    }
    if ($forbidden !== true)
    {
      foreach ($ifNotAvailable as $key)
      {
        if (!array_key_exists(strtolower($key), $params))
        {
          $forbidden = true;
          break;
        }
      }
    }
    if ($forbidden === true)
    {
      if (in_array($configuration->getType(), array(TypeEnum::COLLECTION_TYPE, TypeEnum::OBJECT_TYPE)))
      {
        array_push($constraints, new EmptyArrayOrNull());
      }
      else
      {
        array_push($constraints, new Constraints\Null());
      }
      $this->validateConstrains($constraints, $parameter);
    }
  }



  /**
   * @param Configuration $configuration
   */
  protected function validateType(Configuration $configuration)
  {
    $this->addConstraint(new Constraints\Type(array('type' => $configuration->getType())));
  }



  /**
   * @param array $expected
   * @param bool  $multiple
   */
  protected function validateExpected(array $expected, $multiple = false)
  {
    if (count($expected))
    {
      $config = array(
        'choices'  => $expected,
        'multiple' => $multiple
      );

      $this->addConstraint(new Constraints\Choice($config));
    }
  }



  /**
   * @param $min
   * @param $max
   */
  protected function validateMinMax($min, $max)
  {
    if ($min !== null)
    {
      $this->addConstraint(new Constraints\GreaterThanOrEqual(array('value' => $min)));
    }
    if ($max !== null)
    {
      $this->addConstraint(new Constraints\LessThanOrEqual(array('value' => $max)));
    }
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function validateArray(Configuration $configuration, Parameter $parameter)
  {
    $this->validateType($configuration);
    $this->validateExpected($configuration->getOptions()->getExpected(), true);

    // RegEx
    if ($configuration->getOptions()->getRegex() !== null)
    {
      $this->addConstraint(
        new ArrayRegex(
          array(
            'pattern' => $configuration->getOptions()->getRegex()
          )
        )
      );
    }

    $this->dispatcher->dispatch(
      TransformerEvents::VALIDATE_ARRAY,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function validateBool(Configuration $configuration, Parameter $parameter)
  {
    $this->validateType($configuration);
    $this->validateExpected($configuration->getOptions()->getExpected());

    $this->dispatcher->dispatch(
      TransformerEvents::VALIDATE_BOOL,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function validateCollection(Configuration $configuration, Parameter $parameter)
  {
    $this->addConstraint(
      new Constraints\Collection(array('fields' => array(), 'allowExtraFields' => true))
    );
    $this->dispatcher->dispatch(
      TransformerEvents::VALIDATE_COLLECTION,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function validateDate(Configuration $configuration, Parameter $parameter)
  {
    $this->addConstraint(
      new Date(array('format' => $configuration->getOptions()->getExpectedFormat()))
    );

    $this->dispatcher->dispatch(
      TransformerEvents::VALIDATE_DATE,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function validateFloat(Configuration $configuration, Parameter $parameter)
  {
    $this->validateType($configuration);

    if ($configuration->getOptions()->getRound() !== null)
    {
      $parameter->setValue(
        round(
          $parameter->getValue(),
          $configuration->getOptions()->getRound(),
          PHP_ROUND_HALF_DOWN
        )
      );
    }

    $this->validateMinMax(
      $configuration->getOptions()->getMin(),
      $configuration->getOptions()->getMax()
    );
    $this->validateExpected($configuration->getOptions()->getExpected());

    $this->dispatcher->dispatch(
      TransformerEvents::VALIDATE_FLOAT,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function validateInteger(Configuration $configuration, Parameter $parameter)
  {
    $this->validateType($configuration);

    $this->validateMinMax(
      $configuration->getOptions()->getMin(),
      $configuration->getOptions()->getMax()
    );
    $this->validateExpected($configuration->getOptions()->getExpected());

    $this->dispatcher->dispatch(
      TransformerEvents::VALIDATE_INTEGER,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function validateIndividual(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::VALIDATE_INDIVIDUAL,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function validateObject(Configuration $configuration, Parameter $parameter)
  {
    $this->dispatcher->dispatch(
      TransformerEvents::VALIDATE_OBJECT,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   * @param Parameter     $parameter
   */
  protected function validateString(Configuration $configuration, Parameter $parameter)
  {
    $this->validateType($configuration);
    $this->validateExpected($configuration->getOptions()->getExpected());
    // String-Length
    if ($configuration->getOptions()->getMin() !== null
        && $configuration->getOptions()->getMax() !== null
    )
    {
      $this->addConstraint(
        new Constraints\Length(
          array(
            'min' => $configuration->getOptions()->getMin(),
            'max' => $configuration->getOptions()->getMax(),
          )
        )
      );
    }

    $this->specialValidation($configuration);

    $this->dispatcher->dispatch(
      TransformerEvents::VALIDATE_STRING,
      new ValidatorEvent($configuration, $parameter, $this)
    );
  }



  /**
   * @param Configuration $configuration
   */
  protected function specialValidation(Configuration $configuration)
  {
    // RegEx
    if ($configuration->getOptions()->getRegex() !== null)
    {
      $this->addConstraint(
        new Constraints\Regex(
          array(
            'pattern' => $configuration->getOptions()->getRegex()
          )
        )
      );
    }
    // Special Validation
    switch ($configuration->getOptions()->getValidation())
    {
      case StringValidationEnum::EMAIL:
        $this->addConstraint(
          new Constraints\Email(
            array(
              'checkMX'   => $configuration->getOptions()->isStrong(),
              'checkHost' => $configuration->getOptions()->isStrong()
            )
          )
        );
        break;
      case StringValidationEnum::URL:
        $this->addConstraint(new Constraints\Url(array('protocols' => array('http', 'https', 'ftp'))));
        break;
      case StringValidationEnum::IP:
        $this->addConstraint(new Constraints\Ip(array('version' => 'all')));
        break;
    }
  }
}
