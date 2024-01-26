<?php

namespace Drupal\yale_cas\EventSubscriber;

use Drupal\cas\Event\CasPreRedirectEvent;
use Drupal\cas\Service\CasHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Modifies the route system.
 */
class CasAttributesSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[CasHelper::EVENT_PRE_REDIRECT][] = ['onCasPreRedirect'];

    return $events;
  }

  /**
   *
   */
  public function onCasPreRedirect(CasPreRedirectEvent $event) {
    $casRedirectData = $event->getCasRedirectData();
    $casRedirectData->setServiceParameter('app', 'yalesites');
  }

}
