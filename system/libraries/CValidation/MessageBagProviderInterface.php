<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 30, 2019, 3:34:01 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CValidation_MessageBagProviderInterface {

    /**
     * Get the messages for the instance.
     *
     * @return CValidation_MessageBag
     */
    public function getMessageBag();
}
