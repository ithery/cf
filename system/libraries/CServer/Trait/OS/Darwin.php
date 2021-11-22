<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 7, 2019, 2:42:52 PM
 */
trait CServer_Trait_OS_Darwin {
    /**
     * Get a value from sysctl command.
     *
     * @param string $key key of the value to get
     *
     * @return string
     */
    protected function grabkey($key) {
        $cmd = $this->createCommand();
        $s = '';
        if ($cmd->executeProgram('sysctl', $key, $s, CServer::config()->isDebug())) {
            $s = preg_replace('/' . $key . ': /', '', $s);
            $s = preg_replace('/' . $key . ' = /', '', $s);

            return $s;
        } else {
            return '';
        }
    }
}
