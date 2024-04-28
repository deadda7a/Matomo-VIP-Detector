<?php

namespace Piwik\Plugins\VipDetector\tests\Integration;

use Exception;
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
        $result = DbHelper::tableExists(Common::prefixTable('vip_detector_names'));
        self::assertTrue($result);
    }

    public function testNameInsert()
    {
        Dao\DatabaseMethods::insertName('Testname');
    }

    public function testInsertRangeValidIp4()
    {
        Dao\DatabaseMethods::insertRange(
            [
                4,
                '203.0.113.0',
                '203.0.113.255',
                1
            ]
        );
    }

    public function testInsertRangeValidIp6()
    {
        Dao\DatabaseMethods::insertRange(
            [
                6,
                '2001:0db8:0000:0000:0000:0000:0000:0000',
                '2001:0db8:ffff:ffff:ffff:ffff:ffff:ffff',
                1
            ]
        );
    }

    public function testInsertRangeInalidIp4()
    {
        $this->expectException(Exception::class);
        Dao\DatabaseMethods::insertRange(
            [
                4,
                '555',
                'asdasd',
                1
            ]
        );
    }

    public function testInsertRangeInalidIp6()
    {
        $this->expectException(Exception::class);
        Dao\DatabaseMethods::insertRange(
            [
                6,
                '555',
                'asdasd',
                1
            ]
        );
    }
}
