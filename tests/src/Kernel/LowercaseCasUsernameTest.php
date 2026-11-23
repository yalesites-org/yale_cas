<?php

namespace Drupal\Tests\yale_cas\Kernel;

use Drupal\cas\Form\BulkAddCasUsers;
use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Drupal\user\Entity\User;

/**
 * Tests that CAS usernames are stored in lowercase.
 *
 * @group yale_cas
 */
class LowercaseCasUsernameTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'externalauth',
    'cas',
    'token',
    'cas_attributes',
    'yale_cas',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installSchema('externalauth', ['authmap']);
    $this->installConfig(['user', 'cas']);
    // Take uid 1 so the admin created below is not a superuser by accident.
    $this->createUser([], 'placeholder');
    $this->setCurrentUser($this->createUser(['administer users']));
  }

  /**
   * The update hook lowercases authnames and skips case-only collisions.
   */
  public function testUpdateHookLowercasesAuthmap(): void {
    $mixed = $this->createUser();
    $lower = $this->createUser();
    $digits = $this->createUser();
    $dupA = $this->createUser();
    $dupB = $this->createUser();
    $other = $this->createUser();
    $this->insertAuthmap((int) $mixed->id(), 'JDoe12', 'cas');
    $this->insertAuthmap((int) $lower->id(), 'already', 'cas');
    // A numeric authname becomes an integer array key; it must not count.
    $this->insertAuthmap((int) $digits->id(), '12345', 'cas');
    $this->insertAuthmap((int) $dupA->id(), 'Dup', 'cas');
    $this->insertAuthmap((int) $dupB->id(), 'dup', 'cas');
    $this->insertAuthmap((int) $other->id(), 'OtherProvider', 'other');

    // Before the update, the lowercase NetID Yale CAS returns does not match.
    $externalauth = $this->container->get('externalauth.externalauth');
    $this->assertFalse($externalauth->load('jdoe12', 'cas'));

    \Drupal::moduleHandler()->loadInclude('yale_cas', 'install');
    $message = (string) yale_cas_update_10001();
    $this->assertStringContainsString('Lowercased 1 CAS usernames', $message);
    $this->assertStringContainsString('Skipped 1 case-only duplicates', $message);

    $this->assertSame([
      $mixed->id() => 'jdoe12',
      $lower->id() => 'already',
      $digits->id() => '12345',
      $dupA->id() => 'Dup',
      $dupB->id() => 'dup',
      $other->id() => 'OtherProvider',
    ], $this->authmap());

    // The person saved with capitals can now log in.
    $loaded = $externalauth->load('jdoe12', 'cas');
    $this->assertNotFalse($loaded);
    $this->assertSame($mixed->id(), $loaded->id());
  }

  /**
   * Bulk Add CAS Users saves the authname, name, and email in lowercase.
   */
  public function testBulkAddLowercases(): void {
    $form_state = (new FormState())->setValues([
      'cas_usernames' => "JDoe12\n  MixedCase  ",
      'email_hostname' => 'yale.edu',
      'roles' => [],
    ]);
    $this->container->get('form_builder')->submitForm(BulkAddCasUsers::class, $form_state);
    $this->assertSame([], $form_state->getErrors());

    foreach (['jdoe12', 'mixedcase'] as $name) {
      $user = $this->container->get('externalauth.externalauth')->load($name, 'cas');
      $this->assertNotFalse($user, "$name was registered with a lowercase authname.");
      $this->assertSame($name, $user->getAccountName());
      $this->assertSame("$name@yale.edu", $user->getEmail());
    }
  }

  /**
   * The Add user form saves the CAS username in lowercase.
   */
  public function testAddUserFormLowercases(): void {
    $form_object = $this->container->get('entity_type.manager')
      ->getFormObject('user', 'register')
      ->setEntity(User::create());
    $form_state = (new FormState())->setValues([
      'name' => 'newperson',
      'mail' => 'newperson@yale.edu',
      'pass' => ['pass1' => 'Secret123!', 'pass2' => 'Secret123!'],
      'status' => 1,
      'cas_enabled' => 1,
      'cas_username' => 'NewPerson',
      'op' => 'Create new account',
    ]);
    $this->container->get('form_builder')->submitForm($form_object, $form_state);
    $this->assertSame([], $form_state->getErrors());

    $this->assertContains('newperson', $this->authmap());
  }

  /**
   * The lowercase handler runs before CAS reads the value on every form.
   */
  public function testHandlerRunsFirst(): void {
    $form_builder = $this->container->get('form_builder');
    $entity_form_builder = $this->container->get('entity.form_builder');
    $forms = [
      'bulk' => $form_builder->getForm(BulkAddCasUsers::class),
      'register' => $entity_form_builder->getForm(User::create(), 'register'),
      'edit' => $entity_form_builder->getForm($this->createUser(), 'default'),
    ];
    foreach ($forms as $key => $form) {
      $this->assertSame('yale_cas_lowercase_cas_usernames', reset($form['#validate']), $key);
    }
  }

  /**
   * Inserts an authmap row.
   */
  protected function insertAuthmap(int $uid, string $authname, string $provider): void {
    \Drupal::database()->insert('authmap')
      ->fields(['uid' => $uid, 'provider' => $provider, 'authname' => $authname])
      ->execute();
  }

  /**
   * Returns authnames keyed by uid.
   */
  protected function authmap(): array {
    return \Drupal::database()->select('authmap', 'a')
      ->fields('a', ['uid', 'authname'])
      ->orderBy('uid')
      ->execute()
      ->fetchAllKeyed();
  }

}
