<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:09:07 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CElastic_Connection_Strategy_StrategyInterface {

    /**
     * @param array|CElastic_Connection[] $connections
     *
     * @return CElastic_Connection
     */
    public function getConnection($connections);
}
