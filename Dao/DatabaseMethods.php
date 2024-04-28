<?php

namespace Piwik\Plugins\VipDetector\Dao;

use Exception;
use Piwik\Db;
use Piwik\DbHelper;
use Piwik\Common;
use Piwik\Plugins\VipDetector\libs\Helpers;
use Piwik\Plugins\VipDetector\libs\NotFoundException;

class DatabaseMethods
{
    /**
     * @throws Exception
     */
    public static function getNameFromIp(string $ip): string
    {
        // We want the name that is associated with this IPs range.
        // So we find the name of the range between the start and the end address and then join it on the names table.
        $query = sprintf(
            'SELECT names.`name`
                FROM `%s` ranges
            LEFT JOIN `%s` names
            ON ranges.`name_id` = names.`id`
                WHERE `type` = ?
                AND  INET6_ATON(?)
                BETWEEN ranges.`range_from` AND ranges.`range_to`',
            Common::prefixTable('vip_detector_ranges'),
            Common::prefixTable('vip_detector_names')
        );

        // We can only have one result, so it is enough to fetch one
        return Db::fetchOne(
            $query,
            array(
                Helpers::getAddressType($ip),
                $ip
            )
        );
    }

    /**
     * @throws Exception
     */
    public static function checkNameInDb(string $table, string $searchValue): string
    {
        $query = sprintf(
            'SELECT `id` FROM `%s` WHERE `name` = ?',
            Common::prefixTable($table)
        );

        // Names are unique, so we only need the first result
        $result = Db::fetchOne(
            $query,
            array($searchValue) // fetchOne expects the parameters to be an array
        );

        if ($result) {
            return $result;
        }

        throw new NotFoundException("Name not in Database!");
    }

    /**
     * @throws Exception
     * @param string $table
     * @param array<string, string> $rangeInfo
     */
    public static function checkRangeInDb(string $table, array $rangeInfo): string
    {
        $query = sprintf(
            'SELECT `id` FROM `%s` WHERE `range_from` = INET6_ATON(?) AND `range_to` = INET6_ATON(?)',
            Common::prefixTable($table)
        );

        // Same idea as with the names
        $result = Db::fetchOne(
            $query,
            array(
                $rangeInfo['range_from'],
                $rangeInfo['range_to']
            )
        );

        if ($result) {
            return $result;
        }

        throw new NotFoundException("Range not in Database!");
    }

    /**
     * @throws Exception
     */
    public static function insertName(string $name): void
    {
        $query = sprintf(
            'INSERT INTO `%s` (name)
                VALUES
            (?)',
            Common::prefixTable('vip_detector_names')
        );

        // an if is not needed because this throws an exception if it fails
        Db::query(
            $query,
            array($name)
        );
    }

    /**
     * @throws Exception
     * @param array<int, string> $rangeInfo
     */
    public static function insertRange(array $rangeInfo): void
    {
        // Store the addresses as INET6_ATON representation for more efficency
        $query = sprintf(
            'INSERT INTO `%s` (type, range_from, range_to, name_id)
                VALUES
            (?, INET6_ATON(?), INET6_ATON(?), ?)',
            Common::prefixTable('vip_detector_ranges')
        );

        // Same as above, no if needed
        Db::query(
            $query,
            $rangeInfo
        );
    }

    /**
     * @throws Exception
     */
    public static function countNames(): int
    {
        return self::countValues('name', 'vip_detector_names');
    }

    /**
     * @throws Exception
     */
    public static function countRanges(): int
    {
        return self::countValues('name_id', 'vip_detector_ranges');
    }

    /**
     * @throws Exception
     */
    private static function countValues(string $to_select, string $table): int
    {
        $result = Db::fetchOne(
            sprintf(
                'SELECT COUNT(DISTINCT "%s") FROM `%s`',
                $to_select,
                Common::prefixTable($table)
            )
        );

        if ($result) {
            return intval($result);
        }

        return 0;
    }

    public static function createTables(): void
    {
        DbHelper::createTable(
            'vip_detector_names',
            'id INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, PRIMARY KEY (id)'
        );

        DbHelper::createTable(
            'vip_detector_ranges',
            'id INT NOT NULL AUTO_INCREMENT, type TINYINT NOT NULL, range_from VARBINARY(16) NOT NULL, range_to VARBINARY(16) NOT NULL, name_id INT NOT NULL, PRIMARY KEY (id)'
        );
    }

    public static function removeTables(): void
    {
        Db::dropTables(
            array(
                Common::prefixTable('vip_detector_names'),
                Common::prefixTable('vip_detector_ranges')
            )
        );
    }
}
