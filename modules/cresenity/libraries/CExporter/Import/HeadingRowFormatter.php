<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Import_HeadingRowFormatter {

    /**
     * @const string
     */
    const FORMATTER_NONE = 'none';

    /**
     * @const string
     */
    const FORMATTER_SLUG = 'slug';

    /**
     * @var string
     */
    protected static $formatter;

    /**
     * @var callable[]
     */
    protected static $customFormatters = [];

    /**
     * @var array
     */
    protected static $defaultFormatters = [
        self::FORMATTER_NONE,
        self::FORMATTER_SLUG,
    ];

    /**
     * @param array $headings
     *
     * @return array
     */
    public static function format(array $headings): array {
        return (new Collection($headings))->map(function ($value) {
                    return static::callFormatter($value);
                })->toArray();
    }

    /**
     * @param string $name
     */
    public static function getDefault($name = null) {
        if (null !== $name && !isset(static::$customFormatters[$name]) && !in_array($name, static::$defaultFormatters, true)) {
            throw new InvalidArgumentException(sprintf('Formatter "%s" does not exist', $name));
        }

        static::$formatter = $name;
    }

    /**
     * @param string   $name
     * @param callable $formatter
     */
    public static function extend($name, callable $formatter) {
        static::$customFormatters[$name] = $formatter;
    }

    /**
     * Reset the formatter.
     */
    public static function reset() {
        static::getDefault();
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected static function callFormatter($value) {
        static::$formatter = static::$formatter ?: CExporter::config()->get('imports.heading_row.formatter', self::FORMATTER_SLUG);

        // Call custom formatter
        if (isset(static::$customFormatters[static::$formatter])) {
            $formatter = static::$customFormatters[static::$formatter];

            return $formatter($value);
        }

        if (static::$formatter === self::FORMATTER_SLUG) {
            return cstr::slug($value, '_');
        }

        // No formatter (FORMATTER_NONE)
        return $value;
    }

}
