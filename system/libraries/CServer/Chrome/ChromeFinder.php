<?php

class CServer_Chrome_ChromeFinder {
    protected $paths = [
        'Darwin' => [
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            '/Applications/Google Chrome Canary.app/Contents/MacOS/Google Chrome Canary',
        ],
        'Linux' => [
            '/usr/bin/google-chrome',
            '/usr/bin/chromium',
        ],
    ];

    public static function forCurrentOperatingSystem() {
        return (new static())->getChromePathForOperatingSystem(PHP_OS);
    }

    public function getChromePathForOperatingSystem(string $operatingSystem) {
        if (!array_key_exists($operatingSystem, $this->paths)) {
            throw CServer_Chrome_Exception_CouldNotTakePageCapture::operatingSystemNotSupported($operatingSystem);
        }

        foreach ($this->paths[$operatingSystem] as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw CServer_Chrome_Exception_CouldNotTakePageCapture::chromeNotFound($this->paths[$operatingSystem]);
    }
}
