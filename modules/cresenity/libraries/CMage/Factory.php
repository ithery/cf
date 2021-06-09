<?php

final class CMage_Factory {
    /**
     * @return \CMage_Option
     */
    public static function createOption() {
        return new CMage_Option();
    }

    /**
     * @param mixed $mage
     * @param mixed $controller
     *
     * @return \CMage_Option
     */
    public static function createCaster($mage, $controller) {
        if (is_string($mage)) {
            $mage = new $mage();
        }
        $caster = new CMage_Caster($mage, $controller);
        return $caster;
    }
}
