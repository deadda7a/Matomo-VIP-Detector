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
    private string $source_type;
    private string $source;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(string $source, string $source_type) {
        $this->logger = StaticContainer::get(LoggerInterface::class);
        $this->source = $source;
        $this->source_type = $source_type;
    }

    public function import(): bool {
        try {
            $sourceData = $this->loadJson($this->source_type, $this->source);
        } catch (Exception $e) {
            $this->logger->critical("Could not load the JSON file: {e}", array('e' => $e->getMessage()));
            return false;
        }

        try {
            $this->insertData($sourceData);
        } catch (Exception $e) {
            $this->logger->critical("Error while inserting: {e}", array('e' => $e->getMessage()));
            return false;
        }

        $this->logger->info("Done loading.");
        return true;
    }

    /**
     * @throws Exception
     */
    private function loadJson($source_type, $source): array  {
        switch ($source_type) {
            case 'file':
                $this->logger->info("Source is file. Start loading");
                // File not found etc
                if (!$string = @file_get_contents($source)) {
                    throw new Exception("File not found.");
                }
                break;

            case 'url':
                if (!SettingsPiwik::isInternetEnabled()) {
                    throw new Exception("To load from a remote URL internet access needs to be enabled.");
                }
                $this->logger->info("Source is remote URL. Start loading.");
                try {
                    $string = Http::sendHttpRequest($source, 30);
                } catch (Exception $e) {
                    throw new Exception($e);
                }

                if (!$string) {
                    throw new Exception("File could not be loaded.");
                }
                break;

            default:
                throw new Exception("Invalid source type.");
        }

        if (!$json = json_decode($string)) {
            throw new Exception("File is not JSON.");
        }

        return $json;
    }

    /**
     * @throws Exception
     */
    private function insertData($data) {
        foreach ($data as $entry) {
            $this->logger->debug($entry->name);
            if (!DatabaseMethods::checkNameInDb('vip_detector_names', $entry->name)) {
                DatabaseMethods::insertName($entry->name);
            }

            foreach ($entry->ranges as $range) {
                $this->logger->debug($range);
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