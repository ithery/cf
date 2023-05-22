<?php

class CValidation_RuleFactory {
    public static function password($min = 8) {
        return new CValidation_Rule_Password($min);
    }
}
