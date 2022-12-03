<?php

namespace Drupal\yale_cas\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Change path '/casservice' to '/cas?app=yalesites'.
    if ($route = $collection->get('cas.service')) {
      $route->setPath('/cas?app=yalesites');
    }
  }

}
