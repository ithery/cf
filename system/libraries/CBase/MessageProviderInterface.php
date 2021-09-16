<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 */
interface CBase_MessageProviderInterface {
    /**
     * Get the messages for the instance.
     *
     * @return CBase_MessageBag
     */
    public function getMessageBag();
}
