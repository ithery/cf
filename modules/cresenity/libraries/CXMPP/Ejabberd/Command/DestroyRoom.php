<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Jun 9, 2020 
 * @license Ittron Global Teknologi
 */
class CXMPP_Ejabberd_Command_DestroyRoom extends CXMPP_Ejabberd_CommandAbstract {

    private $name;
    private $service;

    public function __construct($name, $service) {
        $this->name = $name;
        $this->service = $service;
    }

    public function getCommandName() {
        return 'destroy_room';
    }

    public function getCommandData() {
        /*
          {
          "name": "room1",
          "service": "muc.example.com"
          }
         */

        $data = [
            'name' => $this->name,
            'service' => $this->service,
        ];

        return $data;
    }

}
