<?php

namespace Piwik\Plugins\VipDetector\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\VipDetector\libs\Helpers;

/**
 * @group VipDetector
 * @group HelpersTest
 * @group Plugins
 */
class HelpersTest extends TestCase
{
    public function testAddressTypeDetectionV4()
    {
        $result = Helpers::getAddressType("198.51.100.1");
        $this->assertSame(4, $result);
    }

    public function testAddressTypeDetectionV6()
    {
        $result = Helpers::getAddressType("2001:db8::1");
        $this->assertSame(6, $result);
    }

    public function testAddressRangeBoundsLowerV4()
    {
        $result = Helpers::getRangeInfo("203.0.113.0/24")["range_from"];
        $this->assertSame("203.0.113.0", $result);
    }

    public function testAddressRangeBoundsUpperV4()
    {
        $result = Helpers::getRangeInfo("203.0.113.0/24")["range_to"];
        $this->assertSame("203.0.113.255", $result);
    }

    public function testAddressRangeBoundsLowerV6()
    {
        $result = Helpers::getRangeInfo("2001:db8::/32")["range_from"];
        $this->assertSame("2001:db8::", $result);
    }

    public function testAddressRangeBoundsUpperV6()
    {
        $result = Helpers::getRangeInfo("2001:db8::/32")["range_to"];
        $this->assertSame("2001:db8:ffff:ffff:ffff:ffff:ffff:ffff", $result);
    }

    public function testInvalidRangeV4()
    {
        $this->expectException(\Exception::class);
        Helpers::getRangeInfo("288.644.0.0/24");
    }

    public function testStringInRangeV4()
    {
        $this->expectException(\Exception::class);
        Helpers::getRangeInfo("1.2.3.aa/24");
    }

    public function testInvalidRangeV6()
    {
        $this->expectException(\Exception::class);
        Helpers::getRangeInfo("2001:zzz::/32");
    }
}
