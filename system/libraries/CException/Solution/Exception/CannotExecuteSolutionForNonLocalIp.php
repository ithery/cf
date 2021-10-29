<?php

class CException_Solution_Exception_CannotExecuteSolutionForNonLocalIp extends Exception implements CException_Contract_ProvideSolutionInterface {
    public static function make() {
        return new static('Solutions cannot be run from your current IP address.');
    }

    public function getSolution() {
        return CException::createSolution()
            ->setSolutionTitle('Checking your environment settings')
            ->setSolutionDescription('Solutions can only be executed by requests from a local IP address. Keep in mind that `APP_DEBUG` should set to false on any production environment.');
    }
}
