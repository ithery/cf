<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CMessage
 * @since May 26, 2018, 5:45:00 PM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Message {
    public function set_type($type) {
        return $this->setType($type);
    }

    public function set_message($msg) {
        return $this->setMessage($msg);
    }
}
