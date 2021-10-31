<?php

/**
 * Description of AutoJoinRoom
 *
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since May 31, 2020
 */
class CXMPP_Ejabberd_Command_AutoJoinRoom extends CXMPP_Ejabberd_Command_PrivateSet {
    public function __construct($user, $host, $room, $service, $nick = '') {
        $room = strtolower($room);
        $roomJid = $room . '@' . $service;
        $element = sprintf("<storage xmlns='storage:bookmarks'><conference autojoin='true' jid='%s' name='%s'><nick>%s</nick></conference></storage>", $roomJid, $room, $nick);

        parent::__construct($user, $host, $element);
    }
}
