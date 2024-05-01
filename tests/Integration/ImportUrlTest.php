<?php

namespace Piwik\Plugins\VipDetector\tests\Integration;

use Piwik\Plugins\VipDetector\Dao;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group VipDetector
 * @group ImportUrlTest
 * @group Plugins
 */
class ImportUrlTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        Dao\DatabaseMethods::createTables();
        parent::setUp();
    }

    public function tearDown(): void
    {
        Dao\DatabaseMethods::removeTables();
        parent::tearDown();
    }

    /**
     * All your actual test methods should start with the name "test"
     */
    public function testSimpleAddition()
    {
        $this->assertEquals(2, 1 + 1);
    }
}
