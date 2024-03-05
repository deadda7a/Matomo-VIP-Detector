<?php

namespace Piwik\Plugins\VipDetector;
use Piwik\Http;
use Piwik\Plugins\VipDetector\Dao\DatabaseMethods;
use Piwik\Plugins\VipDetector\libs\Helpers;


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

        if (($json = json_decode(Http::sendHttpRequest($importUrl, 30))) !== null) {
            foreach ($json as $entry) {
                if (!DatabaseMethods::checkNameInDb('vip_detector_names', $entry->name)) {
                    DatabaseMethods::insertName($entry->name);
                }

                foreach ($entry->ranges as $range) {
                    $rangeInfo = Helpers::getRangeInfo($range);
                    if (!DatabaseMethods::checkRangeInDb('vip_detector_ranges', $rangeInfo)) {
                        $nameId = DatabaseMethods::checkNameInDb('vip_detector_names', $entry->name);
                        DatabaseMethods::insertRange(
                            array_merge(
                                $rangeInfo,
                                ['name_id' => $nameId]
                            )
                        );
                    }
                }
            }
        }
    }
}
