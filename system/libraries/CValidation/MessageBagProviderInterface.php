<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 30, 2019, 3:34:01 PM
 */
interface CValidation_MessageBagProviderInterface {
    /**
     * Get the messages for the instance.
     *
     * @return CValidation_MessageBag
     */
    public function getMessageBag();
}
