<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
interface CBase_MessageProviderInterface {

    /**
     * Get the messages for the instance.
     *
     * @return CApp_Message_Bag
     */
    public function getMessageBag();
}
