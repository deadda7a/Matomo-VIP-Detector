<?php
namespace Piwik\Plugins\VipDetector\Commands;

use Piwik\Plugin\ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Piwik\Plugins\VipDetector\Dao\DatabaseMethods;
use Piwik\Plugins\VipDetector\libs\Helpers;

class ImportData extends ConsoleCommand
{
    var $_output; // Console output handler

    protected function configure() {
        $this->setName('vipdetector:import-data');
        $this->setDescription('Import Json File with ranges');
        $this->addArgument(
            'file',
            InputArgument::REQUIRED,
            'Path to the file'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->_output = $output;
        $file = $input->getArgument('file');

        $this->logMessage(sprintf('Loading from file %s', $file));

        // File not found etc
        if(!$string = @file_get_contents($file)) {
            $this->logMessage('Could not load file!', "error");
            exit(1);
        };

        // File is not valid json
        if (!$json = json_decode($string)) {
            $this->logMessage('Could not parse file!', "error");
            exit(1);
        }

        // Loop through the file
        foreach ($json as $entry) {
            // Check if the name is already in the database and insert it if it's not there
            // (should not happen, since they are supposed to be unique, but useful when the file is extended and re-imported)
            if (!DatabaseMethods::checkNameInDb('vip_detector_names', $entry->name)) {
                $this->logMessage(sprintf("Inserting %s", $entry->name));
                DatabaseMethods::insertName($entry->name);
            }

            // Ranges are subkeys of names
            foreach ($entry->ranges as $range) {
                // Get the info from the range
                $rangeInfo = Helpers::getRangeInfo($range);

                // Again, check if it is there already, and if not, store it
                if (!DatabaseMethods::checkRangeInDb('vip_detector_ranges', $rangeInfo)) {
                    // We need to call this a 2nd time, because we also need the ID if no name was inserted in this run
                    $nameId = DatabaseMethods::checkNameInDb('vip_detector_names', $entry->name);
                    // We need the corresponding name id from the other table, and we want everything as one array
                    $this->logMessage(
                        sprintf(
                            'Inserting Range %s to %s with Name ID %s',
                            $rangeInfo['range_from'],
                            $rangeInfo['range_to'],
                            $nameId
                        )
                    );
                    
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

    private function logMessage(string $message, string $level = 'info') {
        if ($level == 'error') {
            $this->_output->writeln(
                sprintf(
                    '<error>Matomo VIP Detector ImportData: %s</error>',
                    $message
                )
            );
        } else {
            $this->_output->writeln(
                sprintf(
                    '<info>Matomo VIP Detector ImportData: %s</info>',
                    $message
                )
            );
        }
    }
}
