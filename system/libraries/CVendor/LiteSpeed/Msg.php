<?php
use CVendor_LiteSpeed_UIBase as UIBase;

class CVendor_LiteSpeed_Msg {
    const LANG_DIR = 'admin/html/res/lang/';

    const DEFAULT_LANG = 'english';

    const LANG_ENGLISH = 'english';

    const LANG_CHINESE = 'chinese';

    const LANG_JAPANES = 'japanes';

    const _COOKIE_LANG_ = 'litespeed_admin_lang';

    private static $supported = [
        self::LANG_ENGLISH => ['English', 'en-US'],
        self::LANG_CHINESE => ['中文', 'zh-CN'],
        self::LANG_JAPANES => ['日本語', 'ja-JP']
    ];

    private static $_curlang = '';

    private static $_curtips = '';

    private static function init() {
        $lang = static::DEFAULT_LANG;

        if (isset($_SESSION[static::_COOKIE_LANG_])) {
            $lang = $_SESSION[static::_COOKIE_LANG_];
        } else {
            $lang1 = UIBase::grabGoodInput('cookie', self::_COOKIE_LANG_);
            if ($lang1 != null && $lang != $lang1 && array_key_exists($lang1, self::$supported)) {
                $lang = $lang1;
            }
            static::setLang($lang);
        }

        $filecode = self::$supported[$lang][1];
        self::$_curlang = $lang;

        $msgfile = CVendor_LiteSpeed::serverRoot() . self::LANG_DIR . 'en-US_msg.php';
        if (file_exists($msgfile)) {
            // maybe called from command line for converter tool
            include $msgfile;

            if ($lang != static::DEFAULT_LANG) {
                include CVendor_LiteSpeed::serverRoot() . self::LANG_DIR . $filecode . '_msg.php';
            }
        }
    }

    private static function initTips() {
        if (self::$_curlang == '') {
            self::init();
        }

        if (self::$_curlang != self::DEFAULT_LANG) {
            $filecode = self::$supported[self::DEFAULT_LANG][1];
            include CVendor_LiteSpeed::serverRoot() . self::LANG_DIR . $filecode . '_tips.php';
        }
        $filecode = self::$supported[self::$_curlang][1];
        self::$_curtips = $filecode . '_tips.php';
        include CVendor_LiteSpeed::serverRoot() . self::LANG_DIR . self::$_curtips;
    }

    public static function getSupportedLang(&$cur_lang) {
        if (self::$_curlang == '') {
            self::init();
        }

        $cur_lang = self::$_curlang;

        return self::$supported;
    }

    public static function setLang($lang) {
        if (PHP_SAPI !== 'cli' && array_key_exists($lang, self::$supported)) {
            $_SESSION[static::_COOKIE_LANG_] = $lang;
            self::$_curlang = '';
            self::$_curtips = '';
            $domain = $_SERVER['HTTP_HOST'];
            if ($pos = strpos($domain, ':')) {
                $domain = substr($domain, 0, $pos);
            }
            $secure = !empty($_SERVER['HTTPS']);
            $httponly = true;

            setcookie(
                static::_COOKIE_LANG_,
                $lang,
                strtotime('+10 days'),
                '/',
                $domain,
                $secure,
                $httponly
            );
        }
    }

    public static function getAttrTip($label) {
        if ($label == '') {
            return null;
        }

        global $_tipsdb;

        if (self::$_curtips == '') {
            self::initTips();
        }

        if (isset($_tipsdb[$label])) {
            return $_tipsdb[$label];
        } else {
            //error_log("DMsg:undefined attr tip $label"); allow null
            return null;
        }
    }

    public static function GetEditTips($labels) {
        global $_tipsdb;

        if (self::$_curtips == '') {
            self::initTips();
        }

        $tips = [];
        foreach ($labels as $label) {
            $label = 'EDTP:' . $label;
            if (isset($_tipsdb[$label])) {
                $tips = array_merge($tips, $_tipsdb[$label]);
            }
        }
        if (empty($tips)) {
            return null;
        } else {
            return $tips;
        }
    }

    public static function UIStr($tag, $repl = null) {
        if ($tag == '') {
            return null;
        }

        global $_gmsg;
        if (self::$_curlang == '') {
            static::init();
        }

        if (isset($_gmsg[$tag])) {
            if ($repl == null) {
                return $_gmsg[$tag];
            }
            $search = array_keys($repl);
            $replace = array_values($repl);

            return str_replace($search, $replace, $_gmsg[$tag]);
        }
        //error_log("DMsg:undefined UIStr tag $tag");
        return 'Unknown';
    }

    public static function EchoUIStr($tag, $repl = '') {
        echo static::UIStr($tag, $repl);
    }

    public static function DocsUrl() {
        if (self::$_curlang == '') {
            static::init();
        }

        $url = '/docs/';
        if (self::$_curlang != self::DEFAULT_LANG) {
            $url .= self::$supported[self::$_curlang][1] . '/';
        }

        return $url;
    }

    public static function aLbl($tag) {
        if ($tag == '') {
            return null;
        }

        global $_gmsg;
        if (self::$_curlang == '') {
            static::init();
        }

        if (isset($_gmsg[$tag])) {
            return $_gmsg[$tag];
        }
        //error_log("DMsg:undefined ALbl tag $tag");
        return 'Unknown';
    }

    public static function err($tag) {
        if ($tag == '') {
            return null;
        }

        global $_gmsg;
        if (self::$_curlang == '') {
            static::init();
        }

        if (isset($_gmsg[$tag])) {
            return $_gmsg[$tag] . ' '; // add extra space
        }
        //error_log("DMsg:undefined Err tag $tag");
        return 'Unknown';
    }

    private static function echoSortKeys($lang_array, $priority) {
        $keys = array_keys($lang_array);
        $key2 = [];
        foreach ($keys as $key) {
            $pos = strpos($key, '_') + 1;
            $key2[substr($key, 0, $pos)][] = substr($key, $pos);
        }

        foreach ($priority as $pri) {
            if (isset($key2[$pri])) {
                sort($key2[$pri]);
                foreach ($key2[$pri] as $subid) {
                    $id = $pri . $subid;
                    echo '$_gmsg[\'' . $id . '\'] = \'' . addslashes($lang_array[$id]) . "'; \n";
                }
                echo "\n\n";
                unset($key2[$pri]);
            }
        }

        if (count($key2) > 0) {
            echo "// *** Not in priority \n";
            print_r($key2);
        }
    }

    public static function Util_SortMsg($lang, $option) {
        if (!array_key_exists($lang, self::$supported)) {
            echo "language ${lang} not supported! \n"
            . 'Currently supported:' . print_r(array_keys(self::$supported), true);

            return;
        }

        global $_gmsg;

        $filecode = self::$supported[$lang][1];
        include 'en-US_msg.php';

        $english = $_gmsg;
        $added = null;
        $_gmsg = null;

        if ($lang != static::DEFAULT_LANG) {
            include $filecode . '_msg.php';
            $added = $_gmsg;
        }

        $header = '<?php

/**
 * WebAdmin Language File
* ' . self::$supported[$lang][0] . '(' . self::$supported[$lang][1] . ')
*
* Please Note: These language files will be overwritten during software updates.
*
* @author     LiteSpeed Technoglogies
* @copyright  Copyright (c) LiteSpeed 2014-2020
* @link       https://www.litespeedtech.com/
*/

global $_gmsg;';

        echo $header . "\n\n";

        $priority = ['menu_', 'tab_', 'btn_', 'note_', 'err_', 'l_', 'o_', 'parse_',
            'service_', 'buildphp_', 'mail_'];

        if (!$added) {
            // output sorted english
            self::echoSortKeys($english, $priority);
        } else {
            if ($option == 'mixed') {
                $mixed = array_merge($english, $added);
                self::echoSortKeys($mixed, $priority);
            } else {
                self::echoSortKeys($added, $priority);

                echo "\n//***** Not in original lang file ***\n\n";

                foreach ($added as $addedkey => $msg) {
                    unset($english[$addedkey]);
                }
                self::echoSortKeys($english, $priority);
            }
        }
    }
}
