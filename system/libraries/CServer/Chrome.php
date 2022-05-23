<?php

class CServer_Chrome {
    /**
     * @param string $url
     *
     * @return CServer_Chrome_PageCapture
     */
    public static function pageCapture($url) {
        return CServer_Chrome_PageCapture::url($url);
    }
}
