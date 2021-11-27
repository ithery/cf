<?php

interface CWebSocket_Contract_AppManagerInterface {
    /**
     * Get all apps.
     *
     * @return array[\CWebSocket_App]
     */
    public function all();

    /**
     * Get app by id.
     *
     * @param string|int $appId
     *
     * @return null|\CWebSocket_App
     */
    public function findById($appId);

    /**
     * Get app by app key.
     *
     * @param string $appKey
     *
     * @return null|\CWebSocket_App
     */
    public function findByKey($appKey);

    /**
     * Get app by secret.
     *
     * @param string $appSecret
     *
     * @return null|\CWebSocket_App
     */
    public function findBySecret($appSecret);
}
