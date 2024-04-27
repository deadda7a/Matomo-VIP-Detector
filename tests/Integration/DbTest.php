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
        $result = DbHelper::tableExists(Common::prefixTable('vip_detector_names'));
        self::assertTrue($result);
    }
}
