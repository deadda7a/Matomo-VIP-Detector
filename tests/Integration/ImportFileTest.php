<?php

namespace Piwik\Plugins\VipDetector\tests\Integration;

use Piwik\Plugins\VipDetector\VipDetector;
use Piwik\Plugins\VipDetector\Dao;
use Piwik\Tests\Framework\TestCase\ConsoleCommandTestCase;

/**
 * @group VipDetector
 * @group ImportFileTest
 * @group Plugins
 */
class ImportFileTest extends ConsoleCommandTestCase
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

    public function testImportFileNotFound()
    {
        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => '/idonotexist'
            )
        );

        $this->assertEquals(1, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('File could not be loaded', $this->applicationTester->getDisplay());
    }

    public function testImportInvalidFileCidr()
    {
        $file = realpath(dirname(__FILE__) . '/../test-assets/invalid-cidr.json');

        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => $file
            )
        );

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Import done.', $this->applicationTester->getDisplay());
    }

    public function testImportInvalidFileIp4()
    {
        $file = realpath(dirname(__FILE__) . '/../test-assets/invalid-ip4.json');

        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => $file
            )
        );

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Import done.', $this->applicationTester->getDisplay());
    }

    public function testImportInvalidFileIp6()
    {
        $file = realpath(dirname(__FILE__) . '/../test-assets/invalid-ip6.json');

        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => $file
            )
        );

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Import done.', $this->applicationTester->getDisplay());
    }

    public function testImportInvalidJson()
    {
        $file = realpath(dirname(__FILE__) . '/../test-assets/invalid-json.json');

        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => $file
            )
        );

        $this->assertEquals(1, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('File is not JSON', $this->applicationTester->getDisplay());
    }

    public function testMissingFileArgument()
    {
        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data'
            )
        );

        $this->assertEquals(1, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Not enough arguments (missing: "file")', $this->applicationTester->getDisplay());
    }

    public function testSuccessfulImportMixed()
    {
        $plugin = new VipDetector();
        $plugin->activate();

        $file = realpath(dirname(__FILE__) . '/../test-assets/valid-mixed.json');

        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => $file
            )
        );

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Import done.', $this->applicationTester->getDisplay());
    }

    public function testSuccessfulImportIp4()
    {
        $plugin = new VipDetector();
        $plugin->activate();

        $file = realpath(dirname(__FILE__) . '/../test-assets/valid-ip4.json');

        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => $file
            )
        );

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Import done.', $this->applicationTester->getDisplay());
    }

    public function testSuccessfulImportIp6()
    {
        $plugin = new VipDetector();
        $plugin->activate();

        $file = realpath(dirname(__FILE__) . '/../test-assets/valid-ip6.json');

        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => $file
            )
        );

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Import done.', $this->applicationTester->getDisplay());
    }

    public function testSuccessfulImportUnicodeNames()
    {
        $plugin = new VipDetector();
        $plugin->activate();

        $file = realpath(dirname(__FILE__) . '/../test-assets/unicode-names.json');

        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => $file
            )
        );

        $this->assertEquals(0, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Import done.', $this->applicationTester->getDisplay());
    }
}
