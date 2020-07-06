<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Jul 6, 2020 
 * @license Ittron Global Teknologi
 */
class CXMPP_Ejabberd_Command_SetRoomAffiliation extends CXMPP_Ejabberd_CommandAbstract {

    private $name;
    private $service;
    private $user;
    private $affiliation;

    public function __construct($user, $name, $service, $affiliation = 'member') {
        $this->name = $name;
        $this->service = $service;
        $this->user = $user;
        $this->affiliation = $affiliation;
    }

    public function getCommandName() {
        return 'set_room_affiliation';
    }

    public function getCommandData() {
        return [
            'name' => $this->name,
            'service' => $this->service,
            'jid' => $this->user,
            'affiliation' => $this->affiliation,
        ];
    }

}
