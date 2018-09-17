<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 4:19:56 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CApp_Message_Contract_MessageProviderInterface {

    /**
     * Get the messages for the instance.
     *
     * @return CApp_Message_Bag
     */
    public function getMessageBag();
}
