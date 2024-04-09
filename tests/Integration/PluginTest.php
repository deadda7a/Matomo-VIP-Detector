<?php

namespace Piwik\Plugins\VipDetector\tests\Integration;

use Piwik\Tests\Framework\TestCase\ConsoleCommandTestCase;

/**
 * @group VipDetector
 * @group PluginTest
 * @group Plugins
 */
class PluginTest extends ConsoleCommandTestCase
{
    public function setUp(): void {
        parent::setUp();

        // set up your test here if needed
    }

    public function tearDown(): void {
        // clean up your test here if needed

        parent::tearDown();
    }

    public function testActivation() {
        $result = $this->applicationTester->run(
            array(
                'command' => 'plugin:activate',
                'plugin' => array('VipDetector')
            )
        );

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('activated.', $this->applicationTester->getDisplay());
    }

    public function testDeactivation() {
        $result = $this->applicationTester->run(
            array(
                'command' => 'plugin:deactivate',
                'plugin' => array('VipDetector')
            )
        );

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Deactivated plugin', $this->applicationTester->getDisplay());
    }
}
