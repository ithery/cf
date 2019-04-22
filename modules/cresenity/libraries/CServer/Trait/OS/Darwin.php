<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 7, 2019, 2:42:52 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CServer_Trait_OS_Darwin {

    /**
     * get a value from sysctl command
     *
     * @param string $key key of the value to get
     *
     * @return string
     */
    protected function grabkey($key) {
        $cmd = $this->createCommand();
        if ($cmd->executeProgram('sysctl', $key, $s, PSI_DEBUG)) {
            $s = preg_replace('/' . $key . ': /', '', $s);
            $s = preg_replace('/' . $key . ' = /', '', $s);
            return $s;
        } else {
            return '';
        }
    }

}
