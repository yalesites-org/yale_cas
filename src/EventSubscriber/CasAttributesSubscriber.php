<?php

namespace Drupal\yale_cas\EventSubscriber;

use Drupal;
use Drupal\cas\Event\CasPreRedirectEvent;
use Drupal\cas\Service\CasHelper;
use Drupal\Core\Extension\ModuleHandlerInterface;
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
    // Set default service parameters.
    $service_parameters = [
      'app' => 'yalesites',
    ];
    // Allow other modules to alter service parameters.
    $this->moduleHandler->invokeAll('yale_cas_pre_redirect_service_parameters_alter', [&$service_parameters, $event]);
    // Set all service parameters.
    foreach ($service_parameters as $key => $value) {
      $casRedirectData->setServiceParameter($key, $value);
    }
  }

}
