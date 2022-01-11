<?php

class CBase {
    const ENVIRONMENT_PRODUCTION = 'production';

    const ENVIRONMENT_DEVELOPMENT = 'development';

    const ENVIRONMENT_STAGING = 'staging';

    const ENVIRONMENT_TESTING = 'testing';

    public static function createRecursionContext() {
        return new CBase_RecursionContext();
    }

    public static function createStringParamable($string, array $params = []) {
        return new CBase_StringParamable($string, $params);
    }
}
