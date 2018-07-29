<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:19:00 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Connection_Strategy_RoundRobin extends CElastic_Connection_Strategy_Simple {

    /**
     * @param array|CElastic_Connection[] $connections
     *
     * @throws CElastic_Exception_ClientException
     *
     * @return CElastic_Connection
     */
    public function getConnection($connections) {
        shuffle($connections);
        return parent::getConnection($connections);
    }

}
