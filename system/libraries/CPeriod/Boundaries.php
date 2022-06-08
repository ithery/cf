<?php

class CPeriod_Boundaries {
    const EXCLUDE_NONE = 0;

    const EXCLUDE_START = 2;

    const EXCLUDE_END = 4;

    const EXCLUDE_ALL = 6;

    /**
     * @var int
     */
    private $mask;

    private function __construct($mask) {
        $this->mask = $mask;
    }

    public static function fromString(string $startBoundary, string $endBoundary): self {
        $matchMap = [
            '[]' => self::EXCLUDE_NONE(),
            '[)' => self::EXCLUDE_END(),
            '(]' => self::EXCLUDE_START(),
            '()' => self::EXCLUDE_ALL(),
        ];

        return carr::get($matchMap, "{$startBoundary}{$endBoundary}");
    }

    /**
     * @return self
     */
    //@codingStandardsIgnoreStart
    public static function EXCLUDE_NONE() {
        //@codingStandardsIgnoreEnd
        return new self(self::EXCLUDE_NONE);
    }

    /**
     * @return self
     */
    //@codingStandardsIgnoreStart
    public static function EXCLUDE_START() {
        //@codingStandardsIgnoreEnd
        return new self(self::EXCLUDE_START);
    }

    /**
     * @return self
     */
    //@codingStandardsIgnoreStart
    public static function EXCLUDE_END() {
        //@codingStandardsIgnoreEnd
        return new self(self::EXCLUDE_END);
    }

    /**
     * @return self
     */
    //@codingStandardsIgnoreStart
    public static function EXCLUDE_ALL() {
        //@codingStandardsIgnoreEnd
        return new self(self::EXCLUDE_ALL);
    }

    /**
     * @return bool
     */
    public function startExcluded() {
        return self::EXCLUDE_START & $this->mask;
    }

    /**
     * @return bool
     */
    public function startIncluded() {
        return !$this->startExcluded();
    }

    /**
     * @return bool
     */
    public function endExcluded() {
        return self::EXCLUDE_END & $this->mask;
    }

    /**
     * @return bool
     */
    public function endIncluded() {
        return !$this->endExcluded();
    }

    /**
     * @param DateTimeImmutable $includedStart
     * @param CPeriod_Precision $precision
     *
     * @return DateTimeImmutable
     */
    public function realStart(DateTimeImmutable $includedStart, CPeriod_Precision $precision) {
        if ($this->startIncluded()) {
            return $includedStart;
        }

        return $precision->decrement($includedStart);
    }

    /**
     * @param DateTimeImmutable $includedEnd
     * @param CPeriod_Precision $precision
     *
     * @return DateTimeImmutable
     */
    public function realEnd(DateTimeImmutable $includedEnd, CPeriod_Precision $precision) {
        if ($this->endIncluded()) {
            return $includedEnd;
        }

        return $precision->increment($includedEnd);
    }
}
