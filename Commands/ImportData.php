<?php

namespace Piwik\Plugins\VipDetector\Commands;

use Piwik\Exception\DI\DependencyException;
use Piwik\Exception\DI\NotFoundException;
use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\VipDetector\RangeUpdater;

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

        new RangeUpdater($file, "file");
    }
}
