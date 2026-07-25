<?php

trait CApp_PWA_Trait_GroupConfigTrait {
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getGroupConfig($key, $default = null) {
        return CF::config('pwa.group.' . $this->group . '.' . $key, $default);
    }
}
