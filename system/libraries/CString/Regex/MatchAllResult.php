<?php

class CString_Regex_MatchAllResult extends CString_Regex_RegexResult {
    protected string $pattern;

    protected string $subject;

    protected bool $result;

    protected array $matches;

    public function __construct(
        string $pattern,
        string $subject,
        bool $result,
        array $matches
    ) {
        $this->pattern = $pattern;
        $this->subject = $subject;
        $this->result = $result;
        $this->matches = $matches;
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @return static
     */
    public static function for(string $pattern, string $subject) {
        $matches = [];

        try {
            $result = preg_match_all($pattern, $subject, $matches, PREG_UNMATCHED_AS_NULL);
        } catch (Exception $exception) {
            throw CString_Regex_Exception_RegexFailedException::match($pattern, $subject, $exception->getMessage());
        }

        if ($result === false) {
            throw CString_Regex_Exception_RegexFailedException::match($pattern, $subject, static::lastPregError());
        }

        return new static($pattern, $subject, $result, $matches);
    }

    public function hasMatch(): bool {
        return $this->result;
    }

    /**
     * @return \CString_Regex_MatchResult[]
     */
    public function results(): array {
        return carr::map(carr::transpose($this->matches), function ($match): CString_Regex_MatchResult {
            return new CString_Regex_MatchResult($this->pattern, $this->subject, true, $match);
        });
    }
}
