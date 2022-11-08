<?php

class CBase_DependencyUtils {
    /**
     * @param string|array $mFunctionNameOrNames
     *
     * @return bool
     */
    public static function functionExistsAndEnabled($mFunctionNameOrNames) {
        static $aCache = null;

        if (\is_array($mFunctionNameOrNames)) {
            foreach ($mFunctionNameOrNames as $sFunctionName) {
                if (!static::functionExistsAndEnabled($sFunctionName)) {
                    return false;
                }
            }

            return true;
        }

        if (empty($mFunctionNameOrNames) || !\function_exists($mFunctionNameOrNames) || !\is_callable($mFunctionNameOrNames)) {
            return false;
        }

        if (null === $aCache) {
            $sDisableFunctions = @\ini_get('disable_functions');
            $sDisableFunctions = \is_string($sDisableFunctions) && 0 < \strlen($sDisableFunctions) ? $sDisableFunctions : '';

            $aCache = \explode(',', $sDisableFunctions);
            $aCache = \is_array($aCache) && 0 < \count($aCache) ? $aCache : [];

            if (\extension_loaded('suhosin')) {
                $sSuhosin = @\ini_get('suhosin.executor.func.blacklist');
                $sSuhosin = \is_string($sSuhosin) && 0 < \strlen($sSuhosin) ? $sSuhosin : '';

                $aSuhosinCache = \explode(',', $sSuhosin);
                $aSuhosinCache = \is_array($aSuhosinCache) && 0 < \count($aSuhosinCache) ? $aSuhosinCache : [];

                if (0 < \count($aSuhosinCache)) {
                    $aCache = \array_merge($aCache, $aSuhosinCache);
                    $aCache = \array_unique($aCache);
                }
            }
        }

        return !\in_array($mFunctionNameOrNames, $aCache);
    }
}
