<?php

namespace Drupal\yale_cas\Service;

use Drupal\cas\Service\CasUserManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Class CasSettings.
 */
class CasSettings {

  /**
   * The CAS user manager service.
   *
   * @var \Drupal\cas\Service\CasUserManager
   */
  protected $casUserManager;

  /**
   * Alter the User form.
   *
   * @param array $form
   *   The user settings form passed by reference.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state of the (entire) configuration form.
   *
   * @return void
   */
  public function userFormAlter(&$form, FormStateInterface $form_state) {
    /** @var \Drupal\user\ProfileForm $profileForm */
    $profileForm = $form_state->getFormObject();
    $user = $profileForm->getEntity();

    if ($this->isCasUser($user)) {
      // Do not allow users to change CAS managed account properties.
      $form['account']['mail']['#disabled'] = TRUE;
      $form['account']['cas_username']['#disabled'] = TRUE;
      $form['account']['name']['#disabled'] = TRUE;
      $form['account']['pass']['#access'] = FALSE;
      $form['account']['current_pass']['#access'] = FALSE;

      // Fields populated from CAS attributes should not be editable.
      // On the YaleSites platform this is the first and last name fields.
      if (isset($form['field_first_name'], $form['field_last_name'])) {
        $form['field_first_name']['#disabled'] = TRUE;
        $form['field_last_name']['#disabled'] = TRUE;
      }
    }
  }

  /**
   * Check if a user has a cas account.
   *
   * @param \Drupal\user\Entity\User $user
   *   A users object.
   *
   * @return boolean
   *   True if the user exists and has a CAS account.
   */
  public function isCasUser(User $user) {
    if (empty($user->id())) {
      return FALSE;
    }
    if (empty($this->casUserManager->getCasUsernameForAccount($user->id()))) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Constructs a new CasSettings object.
   *
   * @param \Drupal\cas\Service\CasUserManager $casUserManager
   *   The CAS user manager service.
   */
  public function __construct(CasUserManager $casUserManager) {
    $this->casUserManager = $casUserManager;
  }

}
