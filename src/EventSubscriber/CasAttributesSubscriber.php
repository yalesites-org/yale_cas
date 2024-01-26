<?php

namespace Drupal\yale_cas\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Modifies the route system.
 */
class CasAttributesSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onKernelRequestCheckCustomParam'];
    return $events;
  }

  /**
   * Checks if the request is on the front page and has a custom parameter.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The event to process.
   */
  public function onKernelRequestCheckCustomParam(RequestEvent $event) {
    // Yale CAS service owners will send CAS attributes back to the website
    // with the query string "?app=yalesites". This event subscriber will direct
    // these requests to the normal CAS service controller.
    $request = $event->getRequest();
    if ($request->query->has('app') && $request->query->get('app') == 'yalesites') {
      $request->attributes->set('_controller', '\Drupal\cas\Controller\ServiceController::handle');
    }
  }

}
