<?php

namespace Piwik\Plugins\VipDetector;

use Piwik\Plugin;
use Piwik\Plugins\VipDetector\Dao\DatabaseMethods;

class VipDetector extends Plugin
{
    public function registerEvents(): array
    {
        return [
            'CronArchive.getArchivingAPIMethodForPlugin' => 'getArchivingAPIMethodForPlugin',
        ];
    }

    // support archiving just this plugin via core:archive
    public function getArchivingAPIMethodForPlugin(&$method, $plugin): void
    {
        if ($plugin == 'VipDetector') {
            $method = 'VipDetector.getExampleArchivedMetric';
        }
    }

    public function activate(): void
    {
        DatabaseMethods::createTables();
    }

    public function uninstall(): void
    {
        DatabaseMethods::removeTables();
    }
}
