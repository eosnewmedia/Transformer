<?php


namespace Enm\Transformer\Helpers;

use Enm\Transformer\Entities\Configuration;
use Enm\Transformer\Entities\Parameter;
use Enm\Transformer\Events\ExceptionEvent;
use Enm\Transformer\Exceptions\TransformerException;
use Enm\Transformer\TransformerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class EnmBaseValidator
{

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * @var array
   */
  protected $constraints;

  /**
   * @var \Symfony\Component\Validator\Validator\ValidatorInterface
   */
  protected $validator;



  /**
   * @param EventDispatcherInterface $dispatcher
   * @param ValidatorInterface       $validatorInterface
   */
  public function __construct(EventDispatcherInterface $dispatcher, ValidatorInterface $validatorInterface)
  {
    $this->dispatcher = $dispatcher;
    $this->validator  = $validatorInterface;
  }



  /**
   * @param Constraint $constraint
   */
  public function addConstraint(Constraint $constraint)
  {
    if (!is_array($this->constraints))
    {
      $this->constraints = array();
    }
    array_push($this->constraints, $constraint);
  }



  /**
   * @return Constraint[]
   */
  public function getConstraints()
  {
    if (!is_array($this->constraints))
    {
      $this->constraints = array();
    }

    return $this->constraints;
  }



  public function clearConstraints()
  {
    $this->constraints = array();
  }



  /**
   * @param Constraint[] $constraints
   * @param Parameter    $parameter
   *
   * @throws TransformerException
   */
  public function validateConstraints(array $constraints, Parameter $parameter)
  {
    try
    {
      $violationList = $this->validator->validate(
        $parameter->getValue(),
        $constraints
      );
    }
    catch (\Exception $e)
    {
      $this->dispatcher->dispatch(TransformerEvents::ON_EXCEPTION, new ExceptionEvent($e));
      $violationList = $this->handleException($e, $parameter);
    }

    if ($violationList->count() > 0)
    {
      $invalidValue = $violationList->get(0)->getInvalidValue();
      if (is_array($invalidValue))
      {
        $invalidValue = '[' . implode(', ', $invalidValue) . ']';
      }
      throw new TransformerException(
        'Key: ' . $parameter->getKey() . ' - ' . 'Value: '
        . $invalidValue . ' - Error: '
        . $violationList->get(0)->getMessage()
      );
    }
  }



  /**
   * @param Constraint[] $constraints
   * @param Parameter    $parameter
   *
   * @throws TransformerException
   * @deprecated will be removed in version 1.0
   */
  public function validateConstrains(array $constraints, Parameter $parameter)
  {
    $this->validateConstraints($constraints, $parameter);
  }



  /**
   * @param \Exception $e
   * @param Parameter  $parameter
   *
   * @return ConstraintViolationList
   */
  protected function handleException(\Exception $e, Parameter $parameter)
  {
    $violation = new ConstraintViolation(
      $e->getMessage(), $e->getMessage(), array(
      $e->getCode(),
      $e->getFile(),
      $e->getLine()
    ), $parameter->getValue(), null, $parameter->getValue()
    );

    return new ConstraintViolationList(array($violation));
  }



  /**
   * @param Configuration $configuration
   * @param array         $params
   *
   * @return bool
   */
  public function requireIfAvailableAnd(Configuration $configuration, array $params)
  {
    $requiredIfAvailable = $configuration->getOptions()->getRequiredIfAvailable();
    if (is_array($requiredIfAvailable->getAnd()) && count($requiredIfAvailable->getAnd()) > 0)
    {
      foreach ($requiredIfAvailable->getAnd() as $key)
      {
        if (!array_key_exists(strtolower($key), $params))
        {
          return false;
          break;
        }
      }

      return true;
    }

    return false;
  }



  /**
   * @param Configuration $configuration
   * @param array         $params
   *
   * @return bool
   */
  public function requireIfAvailableOr(Configuration $configuration, array $params)
  {
    $requiredIfAvailable = $configuration->getOptions()->getRequiredIfAvailable();
    foreach ($requiredIfAvailable->getOr() as $key)
    {
      if (array_key_exists(strtolower($key), $params))
      {
        return true;
        break;
      }
    }

    return false;
  }



  /**
   * @param Configuration $configuration
   * @param array         $params
   *
   * @return bool
   */
  public function requireIfNotAvailableAnd(Configuration $configuration, array $params)
  {
    $requiredIfNotAvailable = $configuration->getOptions()->getRequiredIfNotAvailable();

    if (is_array($requiredIfNotAvailable->getAnd()) && count($requiredIfNotAvailable->getAnd()) > 0)
    {
      foreach ($requiredIfNotAvailable->getAnd() as $key)
      {
        if (array_key_exists(strtolower($key), $params))
        {
          return false;
          break;
        }
      }

      return true;
    }

    return false;
  }



  /**
   * @param Configuration $configuration
   * @param array         $params
   *
   * @return bool
   */
  public function requireIfNotAvailableOr(Configuration $configuration, array $params)
  {
    $requiredIfNotAvailable = $configuration->getOptions()->getRequiredIfNotAvailable();
    foreach ($requiredIfNotAvailable->getOr() as $key)
    {
      if (!array_key_exists(strtolower($key), $params))
      {
        return true;
        break;
      }
    }

    return false;
  }
}
