<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @deprecated 1.8
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
