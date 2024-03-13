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
        $this->source = $source; // Path or Url to the source
        $this->source_type = $source_type; // url or file
    }

    public function import(): bool {
        // Load the json source
        try {
            $sourceData = $this->loadJson($this->source_type, $this->source);
        } catch (Exception $e) {
            $this->logger->critical("Could not load the JSON file: {e}", array('e' => $e->getMessage()));
            return false;
        }

        // Insert it
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
        // At the moment this can be "file" or "url"
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

                // try to download the file
                try {
                    $string = Http::sendHttpRequest($source, 30);
                } catch (Exception $e) {
                    throw new Exception($e);
                }

                // request was ok, but response was empty
                if (!$string) {
                    throw new Exception("File could not be loaded.");
                }

                break;

            default:
                throw new Exception("Invalid source type.");
        }

        // input is not valid json
        if (!$json = json_decode($string)) {
            throw new Exception("File is not JSON.");
        }

        return $json;
    }

    /**
     * @throws Exception
     */
    private function insertData($data) {
        // loop through all elements in the source
        foreach ($data as $entry) {
            $this->logger->debug($entry->name);

            // only insert the name if it is not already there
            if (!DatabaseMethods::checkNameInDb('vip_detector_names', $entry->name)) {
                DatabaseMethods::insertName($entry->name);
            }

            foreach ($entry->ranges as $range) {
                $this->logger->debug($range);

                $rangeInfo = Helpers::getRangeInfo($range);

                // same as for name, don't insert duplicates
                if (!DatabaseMethods::checkRangeInDb('vip_detector_ranges', $rangeInfo)) {
                    // every ip range needs a matching name entry
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