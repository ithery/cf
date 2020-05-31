<?php

/**
 * Description of AutoJoinRoom
 * 
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since May 31, 2020 
 * @license Ittron Global Teknologi
 */
class CXMPP_Ejabberd_Command_AutoJoinRoom extends CXMPP_Ejabberd_Command_PrivateSet {

    public function __construct($user, $host, $room, $service, $nick = "") {

        $this->user = $user;

        $roomJid = $room . '@' . $service;
        $this->element = sprintf("
            <storage xmlns='storage:bookmarks'>
                <conference autojoin='true' jid='%s' name='%s'>
                <nick>%s</nick></conference>
            </storage>
        ", $roomJid, $room, $nick);
        $this->host = $host;
    }

}
