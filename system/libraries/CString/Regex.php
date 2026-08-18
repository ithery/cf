<?php
/**
 * @see CString
 */
class CString_Regex {
    public static function match(string $pattern, string $subject): CString_Regex_MatchResult {
        return CString_Regex_MatchResult::for($pattern, $subject);
    }

    public static function matchAll(string $pattern, string $subject): CString_Regex_MatchAllResult {
        return CString_Regex_MatchAllResult::for($pattern, $subject);
    }

    /**
     * @param string|array          $pattern
     * @param string|array|callable $replacement
     * @param string|array          $subject
     * @param int                   $limit
     */
    public static function replace(
        $pattern,
        $replacement,
        $subject,
        $limit = -1
    ): CString_Regex_ReplaceResult {
        return CString_Regex_ReplaceResult::for($pattern, $replacement, $subject, $limit);
    }
}
