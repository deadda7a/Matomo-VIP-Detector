<?php

namespace Piwik\Plugins\VipDetector;

class Tasks extends \Piwik\Plugin\Tasks {
    public function schedule() {
        $this->hourly('rangeImportTask');
    }

    /**
     * @throws \Exception
     */
    public function rangeImportTask() {
        $settings = new SystemSettings();
        $importUrl = $settings->importUrl->getValue();
        $importViaScheduler=$settings->importViaScheduler->getValue();

        if (!$importViaScheduler) {
            return;
        }

        new RangeUpdater($importUrl, "url");
    }
}
