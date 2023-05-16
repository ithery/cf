<?php

class CLogger_Reader_Util_GenerateCacheKey {
    public static function for($object, $namespace = null) {
        $key = '';

        if ($object instanceof CLogger_Reader_LogFile) {
            $key = self::baseKey() . ':file:' . $object->identifier;
        }

        if ($object instanceof CLogger_Reader_LogIndex) {
            $key = self::for($object->file) . ':' . $object->identifier;
        }

        if (is_string($object)) {
            $key = self::baseKey() . ':' . $object;
        }

        if (!empty($namespace)) {
            $key .= ':' . $namespace;
        }

        return $key;
    }

    protected static function baseKey() {
        return 'capp-lr:' . CF::version();
    }
}
