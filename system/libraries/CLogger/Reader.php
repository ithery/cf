<?php
/**
 * @see https://github.com/opcodesio/log-viewer
 */
class CLogger_Reader {
    const DEFAULT_MAX_LOG_SIZE_TO_DISPLAY = 131_072;    // 128 KB

    protected static $instance;

    /**
     * @var int
     */
    protected $maxLogSizeToDisplay = self::DEFAULT_MAX_LOG_SIZE_TO_DISPLAY;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function cache() {
        return CCache::manager()->driver(CF::config('log.reader.cache_driver', CF::config('cache.default', 'file')));
    }

    /**
     * @param string $file
     *
     * @return CLogger_Reader_LogFile
     */
    public static function createLogFile($file) {
        return new CLogger_Reader_LogFile($file);
    }

    /**
     * @return string
     */
    public static function logRegexPattern() {
        return CF::config(
            'log.reader.patterns.log_parsing_regex',
            '/^\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}\.?(\d{6}([\+-]\d\d:\d\d)?)?)\](.*?(\w+)\.|.*?)('
            . implode('|', array_filter(CLogger_Level::caseValues()))
            . ')?: (.*?)( in [\/].*?:[0-9]+)?$/is'
        );
    }

    /**
     * @return string
     */
    public static function logMatchPattern() {
        return CF::config(
            'log.reader.patterns.log_matching_regex',
            '/^\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}\.?(\d{6}([\+-]\d\d:\d\d)?)?)\].*/',
        );
    }

    /**
     * @return int
     */
    public static function lazyScanChunkSize() {
        return intval(CF::config('log.reader.lazy_scan_chunk_size_in_mb', 100)) * 1024 * 1024;
    }

    /**
     * Get the maximum number of bytes of the log that we should display.
     *
     * @return int
     */
    public function maxLogSize() {
        return $this->maxLogSizeToDisplay;
    }

    /**
     * @param int $bytes
     *
     * @return void
     */
    public function setMaxLogSize($bytes) {
        $this->maxLogSizeToDisplay = $bytes > 0 ? $bytes : self::DEFAULT_MAX_LOG_SIZE_TO_DISPLAY;
    }
}
