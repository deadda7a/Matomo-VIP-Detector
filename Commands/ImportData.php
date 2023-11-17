<?php

namespace Piwik\Plugins\VipDetector\Commands;

use Exception;
use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\VipDetector\Dao\DatabaseMethods;
use Piwik\Plugins\VipDetector\libs\Helpers;

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
     * @throws Exception
     */
    protected function doExecute(): int {
        $output = $this->getOutput();
        $input = $this->getInput();

        $file = $input->getArgument('file');

        $output->writeln(sprintf('Loading from file %s', $file));

        // File not found etc
        if (!$string = @file_get_contents($file)) {
            $output->writeln('<fg=red>Could not load file!</>');
            return self::FAILURE;
        }

        // File is not valid json
        if (!$json = json_decode($string)) {
            $output->writeln('<fg=red>Could not parse file!</>');
            return self::FAILURE;
        }

        // Loop through the file
        foreach ($json as $entry) {
            // Check if the name is already in the database and insert it if it's not there
            if (!DatabaseMethods::checkNameInDb('vip_detector_names', $entry->name)) {
                $output->write(sprintf('Dataset <fg=blue>%s</> inserted, %d ranges to insert: ', $entry->name, count($entry->ranges)));
                DatabaseMethods::insertName($entry->name);
            } else {
                $output->write(sprintf('Dataset <fg=blue>%s</> already in DB, %d range(s) to insert: ', $entry->name, count($entry->ranges)));
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
                    DatabaseMethods::insertRange(
                        array_merge(
                            $rangeInfo,
                            ['name_id' => $nameId]
                        )
                    );

                    $output->write('<fg=green>#</>');
                } else {
                    $output->write('<fg=yellow>.</>');
                }
            }

            $output->writeln('');
        }

        $this->writeSuccessMessage(['Done!']);
        return self::SUCCESS;
    }
}
