<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:17:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Connection_Strategy_Simple implements CElastic_Connection_Strategy_StrategyInterface {

    /**
     * @param array|CElastic_Connection[] $connections
     *
     * @throws CElastic_Exception_ClientException
     *
     * @return CElastic_Connection
     */
    public function getConnection($connections) {
        foreach ($connections as $connection) {
            if ($connection->isEnabled()) {
                return $connection;
            }
        }
        throw new CElastic_Exception_ClientException('No enabled connection');
    }

}
