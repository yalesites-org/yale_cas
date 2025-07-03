<?php

namespace Drupal\yale_cas\EventSubscriber;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\cas\Event\CasPreRedirectEvent;
use Drupal\cas\Service\CasHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Modifies the route system.
 */
class CasAttributesSubscriber implements EventSubscriberInterface {

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * CasAttributesSubscriber constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[CasHelper::EVENT_PRE_REDIRECT][] = ['onCasPreRedirect'];

    return $events;
  }

  /**
   * Append app=yalesites to CAS redirect, allowing override via hook.
   *
   * @param \Drupal\cas\Event\CasPreRedirectEvent $event
   *   The CAS event.
   */
  public function onCasPreRedirect(CasPreRedirectEvent $event) {
    $casRedirectData = $event->getCasRedirectData();
    
    // Set default app parameter value.
    $app_value = 'yalesites';

    // Allow other modules to alter the app parameter value.
    $this->moduleHandler->invokeAll('yale_cas_app_parameter_alter', [&$app_value, $event]);

    // Set the app service parameter.
    $casRedirectData->setServiceParameter('app', $app_value);
  }

}
