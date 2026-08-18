<?php

class CString_Regex_ReplaceResult extends CString_Regex_RegexResult {
    /**
     * @var string|array
     */
    protected $pattern;

    /**
     * @var mixed
     */
    protected $replacement;

    /**
     * @var string|array
     */
    protected $subject;

    /**
     * @var string|array
     */
    protected $result;

    protected int $count;

    public function __construct(
        $pattern,
        $replacement,
        $subject,
        $result,
        int $count
    ) {
        $this->pattern = $pattern;
        $this->replacement = $replacement;
        $this->subject = $subject;
        $this->result = $result;
        $this->count = $count;
    }

    /**
     * @param string|array          $pattern
     * @param string|array|callable $replacement
     * @param string|array          $subject
     * @param int                   $limit
     *
     * @return static
     */
    public static function for(
        $pattern,
        $replacement,
        $subject,
        int $limit
    ) {
        try {
            list($result, $count) = !is_string($replacement) && is_callable($replacement)
                ? static::doReplacementWithCallable($pattern, $replacement, $subject, $limit)
                : static::doReplacement($pattern, $replacement, $subject, $limit);
        } catch (Exception $exception) {
            throw CString_Regex_Exception_RegexFailedException::replace($pattern, $subject, $exception->getMessage());
        }

        if ($result === null) {
            throw CString_Regex_Exception_RegexFailedException::replace($pattern, $subject, static::lastPregError());
        }

        return new static($pattern, $replacement, $subject, $result, $count);
    }

    /**
     * @param string|array          $pattern
     * @param string|array|callable $replacement
     * @param string|array          $subject
     * @param int                   $limit
     *
     * @return array
     */
    protected static function doReplacement(
        $pattern,
        $replacement,
        $subject,
        int $limit
    ): array {
        $count = 0;

        $result = preg_replace($pattern, $replacement, $subject, $limit, $count);

        return [$result, $count];
    }

    /**
     * @param string|array $pattern
     * @param callable     $replacement
     * @param string|array $subject
     * @param int          $limit
     *
     * @return array
     */
    protected static function doReplacementWithCallable(
        $pattern,
        $replacement,
        $subject,
        int $limit
    ): array {
        $replacement = function (array $matches) use ($pattern, $subject, $replacement) {
            return $replacement(new CString_Regex_MatchResult($pattern, $subject, true, $matches));
        };

        $count = 0;

        $result = preg_replace_callback($pattern, $replacement, $subject, $limit, $count);

        return [$result, $count];
    }

    /**
     * @return string|array
     */
    public function result() {
        return $this->result;
    }

    /**
     * @return int
     */
    public function count(): int {
        return $this->count;
    }
}
