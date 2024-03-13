<?php

namespace Piwik\Plugins\VipDetector\Commands;

use Piwik\Exception\DI\DependencyException;
use Piwik\Exception\DI\NotFoundException;
use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\VipDetector\RangeUpdater;
use Piwik\Plugins\VipDetector\SystemSettings;

class ImportData extends ConsoleCommand {
    protected function configure(): void {
        $this->setName('vipdetector:import-data');
        $this->setDescription('Import Json File with ranges');
        $this->addRequiredArgument(
            'file',
            'Path to the file'
        );
    }

    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    protected function doExecute(): int {
        $input = $this->getInput();
        $file = $input->getArgument('file');
        $settings = new SystemSettings();

        // Warn the user if the scheduler import is also enabled
        if ($settings->importViaScheduler->getValue()) {
            $this->getOutput()->writeln("<fg=yellow>========= WARNING ==========</>");
            $this->getOutput()->writeln("<fg=yellow>Scheduler Import is enabled!</>");
            $this->getOutput()->writeln("<fg=yellow>========= WARNING ==========</>");
        }

        $importer = new RangeUpdater($file, "file");

        // try to import
        if (!$importer->import()) {
            $this->getOutput()->writeln("<fg=red>Import failed.</>");
            return self::FAILURE;
        }

        $this->getOutput()->writeln("<fg=green>Import done.</>");
        return self::SUCCESS;
    }
}
