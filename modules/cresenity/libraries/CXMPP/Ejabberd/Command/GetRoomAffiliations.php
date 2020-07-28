<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Jul 6, 2020 
 * @license Ittron Global Teknologi
 */
class CXMPP_Ejabberd_Command_GetRoomAffiliations extends CXMPP_Ejabberd_CommandAbstract {

    private $name;
    private $service;

    public function __construct($name, $service) {
        $this->name = $name;
        $this->service = $service;
    }

    public function getCommandName() {
        return 'get_room_affiliations';
    }

    public function getCommandData() {
        return [
            'name' => $this->name,
            'service' => $this->service
        ];
    }

}
