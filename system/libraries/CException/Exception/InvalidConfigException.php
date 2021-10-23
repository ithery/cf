<?php
class CException_Exception_InvalidConfigException extends Exception implements CException_Contract_ProvideSolutionInterface {
    public static function invalidLogLevel($logLevel): self {
        return new static("Invalid log level `{$logLevel}` specified.");
    }

    public function getSolution() {
        $validLogLevels = array_map(
            fn (string $level) => strtolower($level),
            array_keys(CLogger::getLevels())
        );

        $validLogLevelsString = implode(',', $validLogLevels);

        return CException::createSolution()
            ->setSolutionTitle('You provided an invalid log level')
            ->setSolutionDescription("Please change the log level in your `config/log.php` file. Valid log levels are {$validLogLevelsString}.");
    }
}
