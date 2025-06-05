<?php
/**
 * @file
 * Hooks provided by the Yale CAS module.
 */

/**
 * Allows modules to alter CAS service parameters before redirect at the pre-redirect event.
 *
 * @param array $service_parameters
 *   The associative array of service parameters to be sent to CAS. Default contains ['app' => 'yalesites'].
 * @param \Drupal\cas\Event\CasPreRedirectEvent $event
 *   The CAS event object.
 */
function hook_yale_cas_pre_redirect_service_parameters_alter(array &$service_parameters, $event) {
  // Example: Override the 'app' parameter.
  // $service_parameters['app'] = 'custom_value';
}
