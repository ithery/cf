<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CServer_Trait_OS_Darwin {
    /**
     * @param string $key
     *
     * @return string
     */
    protected function grabkey($key) {
        $s = '';
        if ($this->server->executeProgram('sysctl', $key, $s, $this->server->config()->isDebug())) {
            $s = preg_replace('/' . $key . ': /', '', $s);
            $s = preg_replace('/' . $key . ' = /', '', $s);

            return $s;
        }

        return '';
    }
}
