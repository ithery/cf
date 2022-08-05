<?php

trait CApp_PWA_Trait_GroupConfigTrait {
    public function getGroupConfig($key, $default = null) {
        return CF::config('pwa.group.' . $this->group . '.' . $key, $default);
    }
}
