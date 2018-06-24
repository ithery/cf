<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 5:11:06 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CServer_PhpInfo {

    protected static $instance;
    protected static $info = array();

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CServer_Storage();
        }
        return self::$instance;
    }

    public static function get() {
        if (empty(self::$info)) {
            ob_start();
            @phpinfo();
            $phpinfo = array('phpinfo' => array());
            $matches = array();
            if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (strlen($match[1])) {
                        $phpinfo[$match[1]] = array();
                    } elseif (isset($match[3])) {
                        $keys = array_keys($phpinfo);
                        $phpinfo[end($keys)][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
                    } else {
                        $keys = array_keys($phpinfo);
                        $phpinfo[end($keys)][] = $match[2];
                    }
                }
            }
            self::$info = $phpinfo;
        }
        return self::$info;
    }

    public static function getPhpVersion() {
        return PHP_VERSION;
    }

}
