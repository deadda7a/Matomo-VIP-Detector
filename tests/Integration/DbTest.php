<?php

namespace Piwik\Plugins\VipDetector\tests\Integration;

use Piwik\Tests\Framework\TestCase\IntegrationTestCase;
use Piwik\Plugins\VipDetector\Dao;
use Piwik\Db;

/**
 * @group VipDetector
 * @group DbTest
 * @group Plugins
 */
class DbTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // set up your test here if needed
    }

    public function tearDown(): void
    {
        // clean up your test here if needed

        parent::tearDown();
    }


    public function testSimpleAddition()
    {
        $this->assertEquals(2, 1 + 1);
    }
}
