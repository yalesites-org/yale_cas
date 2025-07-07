<?php
/**
 * @file
 * Hooks provided by the Yale CAS module.
 */

/**
 * Allows modules to alter the CAS 'app' parameter before redirect.
 *
 * @param string $app_value
 *   The app parameter value to be sent to CAS. Defaults to 'yalesites'.
 * @param \Drupal\cas\Event\CasPreRedirectEvent $event
 *   The CAS event object.
 */
function hook_yale_cas_app_parameter_alter(&$app_value, $event) {
  // Example: Override the 'app' parameter.
  $app_value = 'custom_value';
}
