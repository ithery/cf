<?php

use Symfony\Component\Process\Process;

class CServer_Chrome_Exception_CouldNotTakePageCapture extends Exception {
    public static function operatingSystemNotSupported($operatingSystem) {
        return new static("The current operating system `{$operatingSystem}` is not supported");
    }

    /**
     * @param array|string $locations
     *
     * @return static
     */
    public static function chromeNotFound($locations) {
        if (!is_array($locations)) {
            $locations = [$locations];
        }

        $locations = implode(', ', $locations);

        return new static("Did not find Chrome at: {$locations}");
    }

    public static function chromeOutputEmpty($screenShotPath, Process $process) {
        $errorOutput = $process->getErrorOutput();

        return new static("For some reason Chrome did not write a file at `{$screenShotPath}`. Error output: `{$errorOutput}`");
    }
}
