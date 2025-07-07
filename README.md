# Yale CAS Module

## Overview

The Yale CAS module, developed by Yale ITS, facilitates easy integration of the CAS single sign-on system for Drupal sites in the Yale community. Administrators can configure CAS settings and map CAS attributes effortlessly.

## Installation

```bash
composer require yalesites-org/yale_cas
drush en yale_cas
```

## Altering CAS App Parameter via Hook

The Yale CAS module provides a hook that allows other modules to alter the CAS `app` parameter before redirecting to the CAS server. By default, the `app` parameter is set to `yalesites`, but you can override it using the following hook in a custom module:

```php
/**
 * Implements hook_yale_cas_app_parameter_alter().
 */
function MYMODULE_yale_cas_app_parameter_alter(&$app_value, $event) {
  // Override the 'app' parameter.
  $app_value = 'my_custom_app';
}
```

This hook is invoked just before the CAS redirect, allowing you to customize the app parameter sent to the CAS server.

## Contribution / Collaboration

You are welcome to contribute functionality, bug fixes, or documentation to this module. If you would like to suggest a fix or new functionality you may add a new issue to the GitHub issue queue or you may fork this repository and submit a pull request.
