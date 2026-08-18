<?php

abstract class CString_Regex_RegexResult {
    protected static function lastPregError(): string {
        return preg_last_error_msg();
    }
}
