<?php

/**
 * Description of UtilsTrait
 *
 * @author Hery
 */
trait CBase_Trait_UtilsTrait {

    public static function resolveLibraryClassName($name, $folder) {
        $name = str_replace("/", "_", $name);
        $names = explode("_", $name);
        if ($folder != null) {
            $folder = ucfirst($folder);
        }
        $prefix = CF::config('app.prefix');
        if (carr::first($names) == $prefix . $folder) {
            return $name;
        }
        return $prefix . $folder . '_' . $name;
    }

}
