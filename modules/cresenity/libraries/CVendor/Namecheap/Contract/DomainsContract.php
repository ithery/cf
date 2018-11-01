<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 25, 2018, 5:30:37 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Interface DomainsContract
 */
interface CVendor_Namecheap_Contract_DomainsContract {

    /**
     * @return array
     */
    public function getList();

    /**
     * @return array
     */
    public function check();

    /**
     * @return array
     */
    public function renew();

    /**
     * @return CVendor_Namecheap_Interaction_Nameservers
     */
    public function ns();
}
