<?php


namespace Enm\Transformer\Helpers;

use Enm\Transformer\Exceptions\TransformerException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EnmEventHandler
{

  /**
   * @var EnmClassBuilder
   */
  protected $classBuilder;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;



  /**
   * @param EventDispatcherInterface $dispatcher
   * @param EnmClassBuilder          $classBuilder
   */
  public function __construct(EventDispatcherInterface $dispatcher, EnmClassBuilder $classBuilder)
  {
    $this->classBuilder = $classBuilder;
    $this->dispatcher   = $dispatcher;
  }



  /**
   * @param array $events
   *
   * @return $this
   * @throws TransformerException
   */
  public function init(array $events)
  {
    try
    {
      $this->addEventListeners($events['listeners'])->addEventSubscribers($events['subscribers']);
    }
    catch (\Exception $e)
    {
      throw new TransformerException($e->getMessage());
    }

    return $this;
  }



  /**
   * @param array $events
   *
   * @return $this
   * @throws TransformerException
   */
  public function destroy(array $events)
  {
    try
    {
      $this->removeEventListeners($events['listeners'])->removeEventSubscribers($events['subscribers']);
    }
    catch (\Exception $e)
    {
      throw new TransformerException($e->getMessage());
    }

    return $this;
  }



  /**
   * @param array $listeners
   *
   * @return $this
   * @throws TransformerException
   */
  protected function addEventListeners(array $listeners)
  {
    try
    {
      foreach ($listeners as $listener)
      {
        $this->dispatcher->addListener(
          $listener['event'],
          array(
            $listener['class'],
            $listener['method']
          ),
          $listener['priority']
        );
      }
    }
    catch (\Exception $e)
    {
      throw new TransformerException($e->getMessage());
    }

    return $this;
  }



  /**
   * @param array $subscribers
   *
   * @return $this
   * @throws TransformerException
   */
  protected function addEventSubscribers(array $subscribers)
  {
    try
    {
      foreach ($subscribers as $subscriber)
      {
        $this->dispatcher->addSubscriber($this->classBuilder->getObjectInstance($subscriber));
      }
    }
    catch (\Exception $e)
    {
      throw new TransformerException($e->getMessage());
    }

    return $this;
  }



  /**
   * @param array $listeners
   *
   * @return $this
   * @throws TransformerException
   */
  protected function removeEventListeners(array $listeners)
  {
    try
    {
      foreach ($listeners as $listener)
      {
        $this->dispatcher->removeListener(
          $listener['event'],
          array(
            $listener['class'],
            $listener['method']
          )
        );
      }
    }
    catch (\Exception $e)
    {
      throw new TransformerException($e->getMessage());
    }

    return $this;
  }



  /**
   * @param array $subscribers
   *
   * @return $this
   * @throws TransformerException
   */
  protected function removeEventSubscribers(array $subscribers)
  {
    try
    {
      foreach ($subscribers as $subscriber)
      {
        $this->dispatcher->removeSubscriber($this->classBuilder->getObjectInstance($subscriber));
      }
    }
    catch (\Exception $e)
    {
      throw new TransformerException($e->getMessage());
    }

    return $this;
  }
}
 