<?php

class CAuth {
    /**
     * Get Manager instance.
     *
     * @return CAuth_Manager
     */
    public static function manager() {
        return CAuth_Manager::instance();
    }

    public static function gate() {
        return CAuth_Access_Gate::instance();
    }

    /**
     * @return CAuth_ImpersonateManager
     */
    public static function impersonateManager() {
        return CAuth_ImpersonateManager::instance();
    }
}
