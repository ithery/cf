<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CExporter_Trait_MapsCsvSettingsTrait {

    /**
     * @var string
     */
    protected static $delimiter = ',';

    /**
     * @var string
     */
    protected static $enclosure = '"';

    /**
     * @var string
     */
    protected static $lineEnding = PHP_EOL;

    /**
     * @var bool
     */
    protected static $useBom = false;

    /**
     * @var bool
     */
    protected static $includeSeparatorLine = false;

    /**
     * @var bool
     */
    protected static $excelCompatibility = false;

    /**
     * @var string
     */
    protected static $escapeCharacter = '\\';

    /**
     * @var bool
     */
    protected static $contiguous = false;

    /**
     * @var string
     */
    protected static $inputEncoding = 'UTF-8';

    /**
     * @param array $config
     */
    public static function applyCsvSettings(array $config) {
        static::$delimiter = carr::get($config, 'delimiter', static::$delimiter);
        static::$enclosure = carr::get($config, 'enclosure', static::$enclosure);
        static::$lineEnding = carr::get($config, 'line_ending', static::$lineEnding);
        static::$useBom = carr::get($config, 'use_bom', static::$useBom);
        static::$includeSeparatorLine = carr::get($config, 'include_separator_line', static::$includeSeparatorLine);
        static::$excelCompatibility = carr::get($config, 'excel_compatibility', static::$excelCompatibility);
        static::$escapeCharacter = carr::get($config, 'escape_character', static::$escapeCharacter);
        static::$contiguous = carr::get($config, 'contiguous', static::$contiguous);
        static::$inputEncoding = carr::get($config, 'input_encoding', static::$inputEncoding);
    }

}
