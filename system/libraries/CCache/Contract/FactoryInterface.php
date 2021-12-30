<?php

interface CCache_Contract_Factory {
    /**
     * Get a cache store instance by name.
     *
     * @param null|string $name
     *
     * @return \CCache_Contract_Factory
     */
    public function store($name = null);
}
