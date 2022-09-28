<?php

class CBase_Formatter {
    /**
     * @param int $iSize
     * @param int $iRound
     *
     * @return string
     */
    public static function formatFileSize($iSize, $iRound = 0) {
        $aSizes = ['B', 'KB', 'MB'];
        for ($iIndex = 0; $iSize > 1024 && isset($aSizes[$iIndex + 1]); $iIndex++) {
            $iSize /= 1024;
        }

        return \round($iSize, $iRound) . $aSizes[$iIndex];
    }
}
