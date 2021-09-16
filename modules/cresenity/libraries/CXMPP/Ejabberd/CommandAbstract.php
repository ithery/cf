<?php

/**
 * Description of CommandAbstract
 *
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since May 30, 2020
 */
abstract class CXMPP_Ejabberd_CommandAbstract {
    abstract public function getCommandName();

    abstract public function getCommandData();
}
