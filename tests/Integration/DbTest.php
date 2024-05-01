<?php

namespace Piwik\Plugins\VipDetector\tests\Integration;

use Piwik\Common;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;
use Piwik\Plugins\VipDetector\Dao;
use Piwik\DbHelper;

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


    public function testTableCreation()
    {
        Dao\DatabaseMethods::createTables();
        self::assertTrue(
            DbHelper::tableExists(Common::prefixTable('vip_detector_names'))
        );
    }

    public function testNameInsert()
    {
        self::assertTrue(
            Dao\DatabaseMethods::insertName('Testname')
        );
    }

    public function testInsertRangeValidIp4()
    {
        self::assertTrue(
            Dao\DatabaseMethods::insertRange(
                [
                    4,
                    '203.0.113.0',
                    '203.0.113.255',
                    1
                ]
            )
        );
    }

    public function testInsertRangeValidIp6()
    {
        self::assertTrue(
            Dao\DatabaseMethods::insertRange(
                [
                    6,
                    '2001:0db8:0000:0000:0000:0000:0000:0000',
                    '2001:0db8:ffff:ffff:ffff:ffff:ffff:ffff',
                    1
                ]
            )
        );
    }

    public function testInsertRangeInvalidIp4()
    {
        self::assertFalse(
            Dao\DatabaseMethods::insertRange(
                [
                    4,
                    '555',
                    'asdasd',
                    1
                ]
            )
        );
    }

    public function testInsertRangeInvalidIp6()
    {
        self::assertFalse(
            Dao\DatabaseMethods::insertRange(
                [
                    6,
                    '555',
                    'asdasd',
                    1
                ]
            )
        );
    }
}
