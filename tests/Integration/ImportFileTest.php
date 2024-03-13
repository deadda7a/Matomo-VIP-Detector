<?php

namespace Piwik\Plugins\VipDetector\tests\Integration;

use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group VipDetector
 * @group ImportFileTest
 * @group Plugins
 */
class ImportFileTest extends IntegrationTestCase {
    public function setUp(): void {
        parent::setUp();

        // set up your test here if needed
    }

    public function tearDown(): void {
        // clean up your test here if needed

        parent::tearDown();
    }

    /**
     * All your actual test methods should start with the name "test"
     */
    public function testSimpleAddition() {
        $this->assertEquals(2, 1 + 1);
    }
}
