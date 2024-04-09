<?php

namespace Piwik\Plugins\VipDetector\tests\Integration;

use Piwik\Tests\Framework\TestCase\ConsoleCommandTestCase;

/**
 * @group VipDetector
 * @group ImportFileTest
 * @group Plugins
 */
class ImportFileTest extends ConsoleCommandTestCase {
    public function setUp(): void {
        parent::setUp();

        // set up your test here if needed
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
        self::assertStringContainsString('Import failed.', $this->applicationTester->getDisplay());
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
}
