<?php

namespace Piwik\Plugins\VipDetector\tests\Integration;

use Piwik\Plugins\VipDetector\VipDetector;
use Piwik\Tests\Framework\TestCase\ConsoleCommandTestCase;

/**
 * @group VipDetector
 * @group ImportFileTest
 * @group Plugins
 */
class ImportFileTest extends ConsoleCommandTestCase {
    public function setUp(): void {
        parent::setUp();
    }

    public function tearDown(): void {
        // clean up your test here if needed

        parent::tearDown();
    }

    public function testImportFileNotFound() {
        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => '/idonotexist'
            )
        );

        $this->assertEquals(1, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('File not found', $this->applicationTester->getDisplay());
    }

    public function testImportInvalidFile() {
        $file = realpath(dirname(__FILE__) . '/../../LICENSE');

        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data',
                'file' => $file
            )
        );

        $this->assertEquals(1, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('File is not JSON', $this->applicationTester->getDisplay());
    }

    public function testMissingFileArgument() {
        $result = $this->applicationTester->run(
            array(
                'command' => 'vipdetector:import-data'
            )
        );

        $this->assertEquals(1, $result, $this->getCommandDisplayOutputErrorMessage());
        self::assertStringContainsString('Not enough arguments (missing: "file")', $this->applicationTester->getDisplay());
    }

    public function testSuccessfulImport() {
        $plugin = new VipDetector;
        $plugin->activate();

        $file = realpath(dirname(__FILE__) . '/../../sample.json');

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
