<?php

namespace Piwik\Plugins\VipDetector\libs;

use Matomo\Network\IP;
use Matomo\Network\IPUtils;
use Matomo\Network\IPv6;

class Helpers {
    public static function getRangeInfo(string $range): array {
        // Get the type (Ipv4/IPv6) and the first and last address of the subnet
        $rangeBounds = IPUtils::getIPRangeBounds($range);

        // TODO: array_walk
        $from = IPUtils::binaryToStringIP($rangeBounds[0]);
        $to = IPUtils::binaryToStringIP($rangeBounds[1]);

        return [
            'type' => self::getAddressType($from), // We could also do this with the last IP
            'range_from' => $from,
            'range_to' => $to
        ];
    }

    public static function getAddressType(string $ip): int {
        $ipObj = IP::fromStringIP($ip);

        if ($ipObj instanceof IPv6) {
            return 6;
        }

        return 4;
    }
}