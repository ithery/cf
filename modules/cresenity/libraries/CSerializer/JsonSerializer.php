<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 23, 2018, 12:04:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CSerializer_JsonSerializer extends CSerializer_Serializer {

    /**
     *
     */
    public function __construct() {
        parent::__construct(new CSerializer_Strategy_JsonStrategy());
    }

}
