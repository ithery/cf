<?php

class CHouseKeeping {
    /**
     * @return CHouseKeeping_Database
     */
    public static function database() {
        return new CBase_ForwarderStaticClass(CHouseKeeping_Database::class);
    }

    /**
     * @return CHouseKeeping_FileTemp
     */
    public static function fileTemp() {
        return new CBase_ForwarderStaticClass(CHouseKeeping_FileTemp::class);
    }
}
