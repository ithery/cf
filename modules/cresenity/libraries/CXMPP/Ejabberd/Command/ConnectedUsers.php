<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Jul 6, 2020
 */
class CXMPP_Ejabberd_Command_ConnectedUsers extends CXMPP_Ejabberd_CommandAbstract {
    /**
     * @var bool
     */
    private $full_info;

    public function __construct($full_info = false) {
        $this->full_info = $full_info;
    }

    public function getCommandName() {
        return $this->full_info ? 'connected_users_info' : 'connected_users';
    }

    public function getCommandData() {
        return [];
    }
}
