<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 11:36:02 PM
 */
class CHelper_IpAddress {
    /**
     * Method CIDRtoMask
     * Return a netmask string if given an integer between 0 and 32. I am
     * not sure how this works on 64 bit machines.
     * Usage:
     *     CIDR::CIDRtoMask(22);
     * Result:
     *     string(13) "255.255.252.0"
     *
     * @param $int int Between 0 and 32
     * @static
     *
     * @return string Netmask ip address
     */
    public static function CIDRtoMask($int) {
        return long2ip(-1 << (32 - (int) $int));
    }

    /**
     * Method countSetBits.
     * Return the number of bits that are set in an integer.
     * Usage:
     *     CIDR::countSetBits(ip2long('255.255.252.0'));
     * Result:
     *     int(22)
     *
     * @param $int int a number
     * @static
     *
     * @see http://stackoverflow.com/questions/109023/best-algorithm-to-co\
     * unt-the-number-of-set-bits-in-a-32-bit-integer
     *
     * @return int number of bits set
     */
    public static function countSetbits($int) {
        $int = $int - (($int >> 1) & 0x55555555);
        $int = ($int & 0x33333333) + (($int >> 2) & 0x33333333);
        return (($int + ($int >> 4) & 0xF0F0F0F) * 0x1010101) >> 24;
    }

    /**
     * Method validNetMask.
     * Determine if a string is a valid netmask.
     * Usage:
     *     CIDR::validNetMask('255.255.252.0');
     *     CIDR::validNetMask('127.0.0.1');
     * Result:
     *     bool(true)
     *     bool(false)
     *
     * @param $netmask string a 1pv4 formatted ip address
     *
     * @return bool
     *
     * @see http://www.actionsnip.com/snippets/tomo_atlacatl/calculate-if-\
     * a-netmask-is-valid--as2-
     * @static
     * return bool True if a valid netmask.
     */
    public static function validNetMask($netmask) {
        $netmask = ip2long($netmask);
        $neg = ((~(int) $netmask) & 0xFFFFFFFF);
        return (($neg + 1) & $neg) === 0;
    }

    /**
     * Method maskToCIDR.
     * Return a CIDR block number when given a valid netmask.
     * Usage:
     *     CIDR::maskToCIDR('255.255.252.0');
     * Result:
     *     int(22)
     *
     * @param $netmask string a 1pv4 formatted ip address
     *
     * @throws Exception
     * @static
     *
     * @return int CIDR number
     */
    public static function maskToCIDR($netmask) {
        if (self::validNetMask($netmask)) {
            return self::countSetBits(ip2long($netmask));
        } else {
            throw new Exception('Invalid Netmask');
        }
    }

    /**
     * Method alignedCIDR.
     * It takes an ip address and a netmask and returns a valid CIDR
     * block.
     * Usage:
     *     CIDR::alignedCIDR('127.0.0.1','255.255.252.0');
     * Result:
     *     string(12) "127.0.0.0/22"
     *
     * @param $ipinput string a IPv4 formatted ip address
     * @param $netmask string a 1pv4 formatted ip address
     * @static
     *
     * @return string CIDR block
     */
    public static function alignedCIDR($ipinput, $netmask) {
        $alignedIP = long2ip((ip2long($ipinput)) & (ip2long($netmask)));
        return "$alignedIP/" . self::maskToCIDR($netmask);
    }

    /**
     * Method IPisWithinCIDR.
     * Check whether an IP is within a CIDR block.
     * Usage:
     *     CIDR::IPisWithinCIDR('127.0.0.33','127.0.0.1/24');
     *     CIDR::IPisWithinCIDR('127.0.0.33','127.0.0.1/27');
     * Result:
     *     bool(true)
     *     bool(false)
     *
     * @param $ipinput string a IPv4 formatted ip address
     * @param $cidr String a IPv4 formatted CIDR block. Block is aligned during execution.
     *
     * @static
     *
     * @return string CIDR block
     */
    public static function IPisWithinCIDR($ipinput, $cidr) {
        $cidr = explode('/', $cidr);
        $cidr = self::alignedCIDR($cidr[0], self::CIDRtoMask((int) $cidr[1]));
        $cidr = explode('/', $cidr);
        $ipinput = (ip2long($ipinput));
        $ip1 = (ip2long($cidr[0]));
        $ip2 = ($ip1 + pow(2, (32 - (int) $cidr[1])) - 1);
        return (($ip1 <= $ipinput) && ($ipinput <= $ip2));
    }

    /**
     * Method maxBlock.
     * Determines the largest CIDR block that an IP address will fit into.
     * Used to develop a list of CIDR blocks.
     * Usage:
     *     CIDR::maxBlock("127.0.0.1");
     *     CIDR::maxBlock("127.0.0.0");
     * Result:
     *     int(32)
     *     int(8)
     *
     * @param $ipinput string a IPv4 formatted ip address
     * @static
     *
     * @return int CIDR number
     */
    public static function maxBlock($ipinput) {
        return self::maskToCIDR(long2ip(-(ip2long($ipinput) & -(ip2long($ipinput)))));
    }

    /**
     * Method rangeToCIDRList.
     * Returns an array of CIDR blocks that fit into a specified range of
     * ip addresses.
     * Usage:
     *     CIDR::rangeToCIDRList("127.0.0.1","127.0.0.34");
     * Result:
     *     array(7) {
     *       [0]=> string(12) "127.0.0.1/32"
     *       [1]=> string(12) "127.0.0.2/31"
     *       [2]=> string(12) "127.0.0.4/30"
     *       [3]=> string(12) "127.0.0.8/29"
     *       [4]=> string(13) "127.0.0.16/28"
     *       [5]=> string(13) "127.0.0.32/31"
     *       [6]=> string(13) "127.0.0.34/32"
     *     }
     *
     * @param string      $startIPinput a IPv4 formatted ip address
     * @param string|null $endIPinput   a IPv4 formatted ip address
     *
     * @see http://null.pp.ru/src/php/Netmask.phps
     *
     * @return array CIDR blocks in a numbered array
     */
    public static function rangeToCIDRList($startIPinput, $endIPinput = null) {
        $start = ip2long($startIPinput);
        $end = (empty($endIPinput)) ? $start : ip2long($endIPinput);
        while ($end >= $start) {
            $maxsize = self::maxBlock(long2ip($start));
            $maxdiff = 32 - intval(log($end - $start + 1) / log(2));
            $size = ($maxsize > $maxdiff) ? $maxsize : $maxdiff;
            $listCIDRs[] = long2ip($start) . "/$size";
            $start += pow(2, (32 - $size));
        }
        return $listCIDRs;
    }

    /**
     * Method cidrToRange.
     * Returns an array of only two IPv4 addresses that have the lowest ip
     * address as the first entry. If you need to check to see if an IPv4
     * address is within range please use the IPisWithinCIDR method above.
     * Usage:
     *     CIDR::cidrToRange("127.0.0.128/25");
     * Result:
     *     array(2) {
     *       "127.0.0.128",
     *       "127.0.0.255",
     *     }
     *
     * @param $cidr string CIDR block
     *
     * @return array low end of range then high end of range
     */
    public static function cidrToRange($cidr) {
        if (!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(\/[0-9]{1,2})?$/", $cidr)) {
            return false;
        }
        $range = [];
        $cidr = explode('/', $cidr);
        $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int) $cidr[1]))));
        $range[1] = long2ip((ip2long($cidr[0])) + pow(2, (32 - (int) $cidr[1])) - 1);
        return $range;
    }

    /**
     * Method twoIpsToRange.
     * Returns an array of only two IPv4 addresses that have the lowest ip
     * address as the first entry. If you need to check to see if an IPv4
     * Usage:
     *     CIDR::cidrToRange("127.0.0.1-127.0.0.255");
     * Result:
     *     array(2) {
     *       "127.0.0.1",
     *       "127.0.0.255",
     *     }
     *
     * @param $string
     *
     * @return array low end of range then high end of range
     */
    public static function twoIpsToRange($string) {
        if (!preg_match_all("/^((?:\d{1,3}\.?){4})\-((?:\d{1,3}\.?){4})$/", $string, $matches)) {
            return false;
        }
        return [
            $matches[1][0],
            $matches[2][0],
        ];
    }

    public static function ipV4Valid($ip) {
        $isIpAddress = false;
        $isRange = false;
        if ($ip) {
            $ip = str_replace('*', '0', $ip);
            try {
                $isIpAddress = inet_pton($ip) !== false;
            } catch (\Exception $e) {
            }
            try {
                $isRange = static::cidrToRange($ip);
            } catch (\Exception $e) {
            }
            try {
                if (!$isIpAddress && !$isRange) {
                    $isRange = static::twoIpsToRange($ip);
                }
            } catch (\Exception $e) {
            }
        }
        return $ip && ($isIpAddress || $isRange);
    }

    public static function getIpV4Range($cidr) {
        list($ip, $mask) = explode('/', $cidr);
        $maskBinStr = str_repeat('1', $mask) . str_repeat('0', 32 - $mask); //net mask binary string
        $inverseMaskBinStr = str_repeat('0', $mask) . str_repeat('1', 32 - $mask); //inverse mask
        $ipLong = ip2long($ip);
        $ipMaskLong = bindec($maskBinStr);
        $inverseIpMaskLong = bindec($inverseMaskBinStr);
        $netWork = $ipLong & $ipMaskLong;
        $start = $netWork + 1; //去掉网络号 ,ignore network ID(eg: 192.168.1.0)
        $end = ($netWork | $inverseIpMaskLong) - 1; //去掉广播地址 ignore brocast IP(eg: 192.168.1.255)
        return [$start, $end];
    }

    public static function isCidr($ip) {
        if (strpos($ip, '/') === false) {
            return false;
        }
        return static::cidrToRange($ip);
    }

    public static function ipv4InRange($ip, $range) {
        if (is_array($range)) {
            foreach ($range as $iprange) {
                if (static::ipv4InRange($ip, $iprange)) {
                    return true;
                }
            }
            return false;
        }
        // Wildcarded range
        // 192.168.1.*
        if (!cstr::contains($range, '-') && cstr::contains($range, '*')) {
            $range = str_replace('*', '0', $range) . '-' . str_replace('*', '255', $range);
        }
        // Dashed range
        //   192.168.1.1-192.168.1.100
        //   0.0.0.0-255.255.255.255
        if (count($twoIps = explode('-', $range)) == 2) {
            $ip1 = ip2long($twoIps[0]);
            $ip2 = ip2long($twoIps[1]);
            return ip2long($ip) >= $ip1 && ip2long($ip) <= $ip2;
        }
        if (count($twoIps = explode('-', $range)) == 2) {
            $ip1 = ip2long($twoIps[0]);
            $ip2 = ip2long($twoIps[1]);
            return ip2long($ip) >= $ip1 && ip2long($ip) <= $ip2;
        }
        // Masked range or fixed IP
        //   192.168.17.1/16 or
        //   127.0.0.1/255.255.255.255 or
        //   10.0.0.1
        return self::ipV4MatchMask($ip, $range);
    }

    public static function ipv4MatchMask($ip, $network) {
        // Determines if a network in the form of
        //   192.168.17.1/16 or
        //   127.0.0.1/255.255.255.255 or
        //   10.0.0.1
        // matches a given ip
        $ipv4_arr = explode('/', $network);
        if (count($ipv4_arr) == 1) {
            $ipv4_arr[1] = '255.255.255.255';
        }
        $network_long = ip2long($ipv4_arr[0]);
        $x = ip2long($ipv4_arr[1]);
        $mask = long2ip($x) == $ipv4_arr[1] ? $x : 0xffffffff << (32 - $ipv4_arr[1]);
        $ipv4_long = ip2long($ip);
        return ($ipv4_long & $mask) == ($network_long & $mask);
    }
}
