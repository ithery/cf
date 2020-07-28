<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Jul 6, 2020 
 * @license Ittron Global Teknologi
 */
class CXMPP_Ejabberd_Command_ConnectedUsers extends CXMPP_Ejabberd_CommandAbstract {

    /**
     * @var bool
     */
    private $full_info;

    public function __construct($full_info = false) {

        $this->full_info = $full_info;
    }

    function getCommandName() {
        return $this->full_info ? 'connected_users_info' : 'connected_users';
    }

    function getCommandData() {
        return [];
    }

}
