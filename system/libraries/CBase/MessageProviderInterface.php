<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CBase_MessageProviderInterface {
    /**
     * Get the messages for the instance.
     *
     * @return CBase_MessageBag
     */
    public function getMessageBag();
}
