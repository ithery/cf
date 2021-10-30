<?php
class CException_Exception_InvalidConfigException extends Exception implements CException_Contract_ProvideSolutionInterface {
    /**
     * @param int $logLevel
     *
     * @return self
     */
    public static function invalidLogLevel($logLevel) {
        return new static("Invalid log level `{$logLevel}` specified.");
    }

    public function getSolution() {
        $validLogLevels = array_map(
            function ($level) {
                return strtolower($level);
            },
            array_keys(CLogger::getLevels())
        );

        $validLogLevelsString = implode(',', $validLogLevels);

        return CException::createSolution()
            ->setSolutionTitle('You provided an invalid log level')
            ->setSolutionDescription("Please change the log level in your `config/log.php` file. Valid log levels are {$validLogLevelsString}.");
    }
}
