<?php

class CException_Solution_Exception_CannotExecuteSolutionForNonLocalEnvironmentException extends Exception implements CException_Contract_ProvideSolutionInterface {
    public static function make() {
        return new static('Cannot run solution in this environment');
    }

    public function getSolution() {
        return CException::createSolution()
            ->setSolutionTitle('Checking your environment settings')
            ->setSolutionDescription('Runnable solutions are disabled in non-local environments. Keep in mind that `APP_DEBUG` should set to false on any production environment.');
    }
}
