<?php

namespace Piwik\Plugins\VipDetector;

use Piwik\Container\StaticContainer;
use Piwik\Exception\DI\DependencyException;
use Piwik\Exception\DI\NotFoundException;
use Piwik\Http;
use Piwik\Log\LoggerInterface;
use Piwik\Plugins\VipDetector\Dao\DatabaseMethods;
use Piwik\Plugins\VipDetector\libs\Helpers;
use Piwik\SettingsPiwik;
use \Exception;

class RangeUpdater {
    private $logger;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(string $source, string $source_type) {

        $this->logger = StaticContainer::get(LoggerInterface::class);

        if (!SettingsPiwik::isInternetEnabled()) {
            $this->logger->info("Internet access needs to be enabled");
            return;
        }

        try {
            $sourceData = $this->loadJson($source_type, $source);
        } catch (Exception $e) {
            $this->logger->critical("Could not load the JSON file: " . $e);
            return;
        }

        try {
            $this->insertData($sourceData);
        } catch (Exception $e) {
            $this->logger->critical("Error while inserting: " . $e);
            return;
        }
    }

    /**
     * @throws Exception
     */
    private function loadJson($source_type, $source): string  {
        switch ($source_type) {
            case 'file':
                $this->logger->info("Source is file. Start loading");
                // File not found etc
                if (!$string = @file_get_contents($source)) {
                    throw new Exception("File not found!");
                }
                break;

            case 'url':
                $this->logger->info("Source is remote URL. Start loading.");
                try {
                    $string = Http::sendHttpRequest($source, 30);
                } catch (Exception $e) {
                    throw new Exception($e);
                }

                if ($string === null) {
                    throw new Exception("File could not be loaded!");
                }
                break;

            default:
                throw new Exception("Invalid source type");
        }

        if (!$json = json_decode($string)) {
            throw new Exception("File is not JSON");
        }

        return $json;
    }

    /**
     * @throws Exception
     */
    private function insertData($data) {
        foreach ($data as $entry) {
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