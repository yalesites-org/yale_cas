# Yale CAS Module

## Overview

The Yale CAS module, developed by Yale ITS, facilitates easy integration of the CAS single sign-on system for Drupal sites in the Yale community. Administrators can configure CAS settings and map CAS attributes effortlessly.

## Installation

```bash
composer require yalesites-org/yale_cas
drush en yale_cas
```

## Altering CAS Service Parameters via Hook

The Yale CAS module provides a hook that allows other modules to alter the CAS service parameters before redirecting to the CAS server. By default, the `app` service parameter is set to `yalesites`, but you can override or add parameters using the following hook in a custom module:

```php
/**
 * Implements hook_yale_cas_pre_redirect_service_parameters_alter().
 */
function MYMODULE_yale_cas_pre_redirect_service_parameters_alter(array &$service_parameters, $event) {
  // Override the 'app' parameter.
  $service_parameters['app'] = 'my_custom_app';
  // Add or modify other service parameters as needed.
}
```

This hook is invoked just before the CAS redirect, allowing you to customize the service parameters sent to the CAS server.

## Contribution / Collaboration

You are welcome to contribute functionality, bug fixes, or documentation to this module. If you would like to suggest a fix or new functionality you may add a new issue to the GitHub issue queue or you may fork this repository and submit a pull request.
