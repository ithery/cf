<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 23, 2018, 12:04:05 AM
 */
class CSerializer_JsonSerializer extends CSerializer_Serializer {
    public function __construct() {
        parent::__construct(new CSerializer_Strategy_JsonStrategy());
    }
}
