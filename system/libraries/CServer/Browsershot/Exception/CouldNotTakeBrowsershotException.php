<?php

class CServer_Browsershot_Exception_CouldNotTakeBrowsershotException extends Exception {
    /**
     * @param string $screenShotPath
     * @param string $output
     * @param array  $command
     *
     * @return self
     */
    public static function chromeOutputEmpty($screenShotPath, $output, array $command = []) {
        $command = json_encode($command);

        $message = <<<CONSOLE
            For some reason Chrome did not write a file at `{$screenShotPath}`.
            Command
            =======
            {$command}
            Output
            ======
            {$output}
            CONSOLE;

        return new static($message);
    }

    /**
     * @param string $path
     *
     * @return static
     */
    public static function outputFileDidNotHaveAnExtension($path) {
        return new static("The given path `{$path}` did not contain an extension. Please append an extension.");
    }
}
