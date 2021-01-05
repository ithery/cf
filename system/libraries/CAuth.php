<?php

class CAuth {
    /**
     * Get Manager instance
     *
     * @return CAuth_Manager
     */
    public static function manager() {
        return CAuth_Manager::instance();
    }
}
