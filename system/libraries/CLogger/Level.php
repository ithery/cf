<?php

class CLogger_Level {
    const DEBUG = 'debug';

    const INFO = 'info';

    const NOTICE = 'notice';

    const WARNING = 'warning';

    const ERROR = 'error';

    const CRITICAL = 'critical';

    const ALERT = 'alert';

    const EMERGENCY = 'emergency';

    const PROCESSING = 'processing';

    const PROCESSED = 'processed';

    const FAILED = 'failed';

    const NONE = '';

    /**
     * @var string
     */
    public string $value;

    /**
     * @param null|string $value
     */
    public function __construct($value = null) {
        $this->value = $value ?? self::NONE;
    }

    /**
     * @return array
     */
    public static function cases(): array {
        return [
            self::DEBUG,
            self::INFO,
            self::NOTICE,
            self::WARNING,
            self::ERROR,
            self::CRITICAL,
            self::ALERT,
            self::EMERGENCY,
            self::PROCESSING,
            self::PROCESSED,
            self::FAILED,
            self::NONE,
        ];
    }

    /**
     * @param null|string $value
     *
     * @return self
     */
    public static function from($value = null) {
        return new self($value);
    }

    /**
     * @return string
     */
    public function getName() {
        if ($this->value == self::NONE) {
            return 'None';
        }

        return \ucfirst($this->value);
    }

    public function getClass(): string {
        $mappingClass = [
            self::PROCESSED => 'success',
            self::DEBUG => 'info',
            self::INFO => 'info',
            self::NOTICE => 'info',
            self::PROCESSING => 'info',
            self::WARNING => 'warning',
            self::FAILED => 'warning',
            self::ERROR => 'danger',
            self::CRITICAL => 'danger',
            self::ALERT => 'danger',
            self::EMERGENCY => 'danger',

        ];

        return carr::get($mappingClass, $this->value, 'none');
    }

    /**
     * @return array
     */
    public static function caseValues() {
        return self::cases();
    }
}
