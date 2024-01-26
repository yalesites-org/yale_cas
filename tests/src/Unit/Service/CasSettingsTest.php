<?php

namespace Drupal\Tests\yale_cas\Unit\Service;

use Drupal\cas\Service\CasUserManager;
use Drupal\user\Entity\User;
use Drupal\yale_cas\Service\CasSettings;
use PHPUnit\Framework\TestCase;

/**
 * Class CasSettingsTest.
 *
 * @coversDefaultClass \Drupal\yale_cas\Service\CasSettings
 *
 * @group yale_cas
 * @group unit
 */
class CasSettingsTest extends TestCase {

  /**
   * The CasSettings service.
   *
   * @var \Drupal\yale_cas\Service\CasSettings
   */
  protected $casSettings;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    // Mock dependencies.
    $casUserManager = $this->createMock(CasUserManager::class);
    // Create the CasSettings instance.
    $this->casSettings = new CasSettings($casUserManager);
  }

  /**
   * Test case where user does not have an account (such as the add-form.)
   *
   * @covers ::isCasUser
   */
  public function testIsCasUserEmptyUserId() {
    // Mock necessary objects.
    $user = $this->createMock(User::class);

    // Configure expectations for the mock objects.
    $user->expects($this->once())
      ->method('id')
      ->willReturn(NULL);

    // Test the isCasUser method.
    $result = $this->casSettings->isCasUser($user);
    $this->assertFalse($result);
  }

  /**
   * Test case where the user is not managed by CAS.
   *
   * @covers ::isCasUser
   */
  public function testIsCasUserNoCasAccount() {
    // Mock necessary objects.
    $user = $this->createMock(User::class);
    $casUserManager = $this->createMock(CasUserManager::class);

    // Configure expectations for the mock objects.
    $user->expects($this->exactly(2))
      ->method('id')
      ->willReturn(1);

    $casUserManager->expects($this->once())
      ->method('getCasUsernameForAccount')
      ->with(1)
      ->willReturn(NULL);

    // Set the casUserManager property on the CasSettings object.
    $reflection = new \ReflectionClass($this->casSettings);
    $property = $reflection->getProperty('casUserManager');
    $property->setAccessible(true);
    $property->setValue($this->casSettings, $casUserManager);

    // Test the isCasUser method.
    $result = $this->casSettings->isCasUser($user);
    $this->assertFalse($result);
  }

  /**
   * Test case where the user has a CAS managed account.
   *
   * @covers ::isCasUser
   */
  public function testIsCasUserWithCasAccount() {
    // Mock necessary objects.
    $user = $this->createMock(User::class);
    $casUserManager = $this->createMock(CasUserManager::class);

    // Configure expectations for the mock objects.
    $user->expects($this->exactly(2))
      ->method('id')
      ->willReturn(1);

    $casUserManager->expects($this->once())
      ->method('getCasUsernameForAccount')
      ->with(1)
      ->willReturn('cas_username');

    // Set the casUserManager property on the CasSettings object.
    $reflection = new \ReflectionClass($this->casSettings);
    $property = $reflection->getProperty('casUserManager');
    $property->setAccessible(true);
    $property->setValue($this->casSettings, $casUserManager);

    // Test the isCasUser method.
    $result = $this->casSettings->isCasUser($user);
    $this->assertTrue($result);
  }

}
