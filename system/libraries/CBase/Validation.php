<?php

class CBase_Validation {
    /**
     * @param string $sString
     * @param bool   $bTrim   = false
     *
     * @return bool
     */
    public static function notEmptyString($sString, $bTrim = false) {
        return \is_string($sString)
            && (0 < \strlen($bTrim ? \trim($sString) : $sString));
    }

    /**
     * @param int $iPort
     *
     * @return bool
     */
    public static function isPort($iPort) {
        return static::rangeInt($iPort, 0, 65535);
    }

    /**
     * @param int $iNumber
     * @param int $iMin    = null
     * @param int $iMax    = null
     *
     * @return bool
     */
    public static function rangeInt($iNumber, $iMin = null, $iMax = null) {
        return \is_int($iNumber)
           && (null !== $iMin && $iNumber >= $iMin || null === $iMin)
           && (null !== $iMax && $iNumber <= $iMax || null === $iMax);
    }
}
