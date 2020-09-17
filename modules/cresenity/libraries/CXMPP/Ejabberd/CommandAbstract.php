<?php

/**
 * Description of CommandAbstract
 * 
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since May 30, 2020 
 * @license Ittron Global Teknologi
 */
abstract class CXMPP_Ejabberd_CommandAbstract {

    abstract public function getCommandName();

    abstract public function getCommandData();
}
