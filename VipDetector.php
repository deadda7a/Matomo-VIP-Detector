<?php
namespace Piwik\Plugins\VipDetector;

use Piwik\DbHelper;
use Piwik\Db;
use Piwik\Common;
use Piwik\Plugin;

class VipDetector extends Plugin {
    public function registerEvents(): array {
        return [
            'CronArchive.getArchivingAPIMethodForPlugin' => 'getArchivingAPIMethodForPlugin',
        ];
    }

    // support archiving just this plugin via core:archive
    public function getArchivingAPIMethodForPlugin(&$method, $plugin): void {
        if ($plugin == 'VipDetector') {
            $method = 'VipDetector.getExampleArchivedMetric';
        }
    }

    public function activate(): void {
        DbHelper::createTable(
            'vip_detector_names',
            'id INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, PRIMARY KEY (id)'
        );
        DbHelper::createTable(
            'vip_detector_ranges',
            'id INT NOT NULL AUTO_INCREMENT, type TINYINT NOT NULL, range_from VARBINARY(16) NOT NULL, range_to VARBINARY(16) NOT NULL, name_id INT NOT NULL, PRIMARY KEY (id)'
        );
    }

    public function uninstall(): void {
        Db::dropTables(
            array(
                Common::prefixTable('vip_detector_names'),
                Common::prefixTable('vip_detector_ranges')
            )
        );
    }
}
