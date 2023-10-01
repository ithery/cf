<?php

class CNavigation {
    /**
     * @return CNavigation_Manager
     */
    public static function manager() {
        return CNavigation_Manager::instance();
    }
}
