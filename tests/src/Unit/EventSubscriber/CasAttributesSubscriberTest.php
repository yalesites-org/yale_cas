<?php
namespace Drupal\Tests\yale_cas\Unit\EventSubscriber;

use Drupal\yale_cas\EventSubscriber\CasAttributesSubscriber;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\cas\Event\CasPreRedirectEvent;
use PHPUnit\Framework\TestCase;

class CasAttributesSubscriberTest extends TestCase {

  public function testAppParameterCanBeOverriddenByHook() {
    // Mock the module handler to alter the app parameter.
    $module_handler = $this->createMock(ModuleHandlerInterface::class);
    $module_handler->expects($this->once())
      ->method('invokeAll')
      ->with(
        'yale_cas_app_parameter_alter',
        $this->callback(function (&$args) {
          // Simulate a module changing the app parameter.
          $args[0] = 'custom_override';
          return true;
        })
      );

    // Mock CasRedirectData and CasPreRedirectEvent.
    $casRedirectData = $this->getMockBuilder('stdClass')
      ->addMethods(['setServiceParameter'])
      ->getMock();
    $casRedirectData->expects($this->once())
      ->method('setServiceParameter')
      ->with('app', 'custom_override');

    $event = $this->createMock(CasPreRedirectEvent::class);
    $event->method('getCasRedirectData')->willReturn($casRedirectData);

    $subscriber = new CasAttributesSubscriber($module_handler);
    $subscriber->onCasPreRedirect($event);
  }

  public function testDefaultAppParameterWhenNoHook() {
    // Mock the module handler with no hook implementations.
    $module_handler = $this->createMock(ModuleHandlerInterface::class);
    $module_handler->expects($this->once())
      ->method('invokeAll')
      ->with('yale_cas_app_parameter_alter', $this->anything());

    // Mock CasRedirectData and CasPreRedirectEvent.
    $casRedirectData = $this->getMockBuilder('stdClass')
      ->addMethods(['setServiceParameter'])
      ->getMock();
    $casRedirectData->expects($this->once())
      ->method('setServiceParameter')
      ->with('app', 'yalesites');

    $event = $this->createMock(CasPreRedirectEvent::class);
    $event->method('getCasRedirectData')->willReturn($casRedirectData);

    $subscriber = new CasAttributesSubscriber($module_handler);
    $subscriber->onCasPreRedirect($event);
  }
}
