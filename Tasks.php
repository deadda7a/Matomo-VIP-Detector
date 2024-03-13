<?php

namespace Piwik\Plugins\VipDetector;

use Piwik\Container\StaticContainer;
use Piwik\Log\LoggerInterface;

class Tasks extends \Piwik\Plugin\Tasks {
    public function schedule() {
        $this->hourly('rangeImportTask');
    }

    /**
     * @throws \Exception
     */
    public function rangeImportTask() {
        $logger = StaticContainer::get(LoggerInterface::class);
        $settings = new SystemSettings();
        $importUrl = $settings->importUrl->getValue();
        $importViaScheduler=$settings->importViaScheduler->getValue();

        if (!$importViaScheduler) {
            $logger->info("Scheduler is disabled.");
            return;
        }

        $importer = new RangeUpdater($importUrl, "url");

        if (!$importer->import()) {
            $logger->critical("Import failed.");
        }
    }
}
