<?php

use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CBase {
    const ENVIRONMENT_PRODUCTION = 'production';

    const ENVIRONMENT_DEVELOPMENT = 'development';

    const ENVIRONMENT_STAGING = 'staging';

    const ENVIRONMENT_TESTING = 'testing';

    public static function createRecursionContext() {
        return new CBase_RecursionContext();
    }

    public static function createMemoizeResolver(callable $func, callable $resolver = null) {
        return new CBase_MemoizeResolver($func, $resolver);
    }

    public static function createMapCache() {
        return new CBase_MapCache();
    }
}
