<?php

namespace Piwik\Plugins\VipDetector;

use Exception;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Http;
use Piwik\Log\LoggerInterface;
use Piwik\Plugins\VipDetector\Dao\DatabaseMethods;
use Piwik\Plugins\VipDetector\libs\Helpers;
use Piwik\Plugins\VipDetector\libs\NotFoundException;
use Piwik\SettingsPiwik;

class RangeUpdater
{
    private LoggerInterface $logger;
    private string $source_type;
    private string $source;

    /**
     * @throws Exception
     */
    public function __construct(string $source, string $source_type)
    {
        $this->logger = StaticContainer::get(LoggerInterface::class);
        $this->source = $source; // Path or Url to the source
        $this->source_type = $source_type; // url or file
    }

    /**
     * @throws Exception
     */
    public function import(): bool
    {
        // Load the json source
        try {
            $sourceData = $this->loadJson($this->source_type, $this->source);
        } catch (Exception $e) {
            $this->logger->critical("Could not load the JSON file: " . $e->getMessage());
            throw new Exception("Could not load the JSON file: " . $e->getMessage());
        }

        // Insert it
        try {
            $this->insertData($sourceData);
        } catch (Exception $e) {
            $this->logger->critical("Error while inserting: " . $e->getMessage());
            throw new Exception("Error while inserting: " . $e->getMessage());
        }

        $this->logger->info("Done loading.");
        return true;
    }

    /**
     * @throws Exception
     */
    private function loadJson(string $source_type, string $source): array
    {
        // At the moment this can be "file" or "url"
        switch ($source_type) {
            case 'file':
                $this->logger->info("Source is file. Start loading");

                // File not found etc
                if (!$source_string = @file_get_contents($source)) {
                    throw new Exception("File not found.");
                }

                break;

            case 'url':
                if (!SettingsPiwik::isInternetEnabled()) {
                    throw new Exception("To load from a remote URL internet access needs to be enabled.");
                }

                $this->logger->info("Source is remote URL. Start loading.");

                // try to download the file
                try {
                    $source_string = Http::sendHttpRequest($source, 30);
                } catch (Exception $e) {
                    throw new Exception($e);
                }

                // request was ok, but response was empty
                if (!$source_string) {
                    throw new Exception("File could not be loaded.");
                }

                break;

            default:
                throw new Exception("Invalid source type.");
        }

        // input is not valid json
        if (!$json = json_decode($source_string)) {
            throw new Exception("File is not JSON.");
        }

        return $json;
    }

    /**
     * @throws Exception
     */
    private function insertData(array $data): void
    {
        // loop through all elements in the source
        foreach ($data as $entry) {
            $name = Common::sanitizeInputValues($entry->name);
            $this->logger->debug($name);

            // only insert the name if it is not already there
            try {
                DatabaseMethods::checkNameInDb('vip_detector_names', $name);
            } catch (NotFoundException) {
                DatabaseMethods::insertName($name);
            }

            foreach ($entry->ranges as $range) {
                $this->logger->debug($range);

                try {
                    $rangeInfo = Helpers::getRangeInfo($range);
                } catch (Exception) {
                    $this->logger->critical("Invalid range {range}", $range);
                    continue;
                }

                // same as for name, don't insert duplicates
                try {
                    DatabaseMethods::checkRangeInDb('vip_detector_ranges', $rangeInfo);
                } catch (NotFoundException) {
                    $nameId = DatabaseMethods::checkNameInDb('vip_detector_names', $name);

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
