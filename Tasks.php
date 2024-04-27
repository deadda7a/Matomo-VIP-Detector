<?php

namespace Piwik\Plugins\VipDetector;

use Exception;
use Piwik\Container\StaticContainer;
use Piwik\Log\LoggerInterface;

class Tasks extends \Piwik\Plugin\Tasks
{
    public function schedule()
    {
        $this->hourly('rangeImportTask');
    }

    /**
     * @throws Exception
     */
    public function rangeImportTask()
    {
        $logger = StaticContainer::get(LoggerInterface::class);
        $settings = new SystemSettings();
        $importUrl = $settings->importUrl->getValue();
        $importViaScheduler = $settings->importViaScheduler->getValue();

        // Don't run if the scheduler is disabled -> User wants to import using the cli
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
