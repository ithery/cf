<?php

class CVendor_LiteSpeed_KeywordAlias {
    private $aliasmap;

    private $aliaskey;

    private static $instance;

    /**
     * Get singleton instance.
     *
     * @return CVendor_LiteSpeed_KeywordAlias
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function normalizedKey($rawkey) {
        $tool = static::instance();

        return $tool->getNormalizedKey($rawkey);
    }

    public static function shortPrintKey($normalizedkey) {
        $tool = static::instance();

        return $tool->getShortPrintKey($normalizedkey);
    }

    private function __construct() {
        $this->defineAlias();
        $this->aliaskey = [];
        foreach ($this->aliasmap as $nk => $sk) {
            $this->aliaskey[strtolower($sk)] = $nk;
        }
    }

    private function getNormalizedKey($rawkey) {
        $key = strtolower($rawkey);
        if (isset($this->aliaskey[$key])) {
            return $this->aliaskey[$key];
        } else {
            return $key;
        }
    }

    private function getShortPrintKey($normalizedkey) {
        if (isset($this->aliasmap[$normalizedkey])) {
            return $this->aliasmap[$normalizedkey];
        } else {
            return null;
        }
    }

    private function defineAlias() {
        // key is all lower case, value is output case
        $this->aliasmap = [
            //'accesscontrol' => 'acc',
            //'address' => 'addr',
        ];
    }
}
