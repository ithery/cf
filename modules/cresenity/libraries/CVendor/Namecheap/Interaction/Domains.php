<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 25, 2018, 5:23:32 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Class Domains
 * @package Kregel\Namecheap
 */
class CVendor_Namecheap_Interaction_Domains extends CVendor_Namecheap_Interaction implements CVendor_Namecheap_Contract_DomainsContract {

    public function getList() {
        return $this->request('get', 'domains.getList', [], function (array $response) {
                    // Check if we have errors first
                    if (!empty($response['Errors'])) {
                        return $response['Errors'];
                    }
                    $domains = $response['CommandResponse']['DomainGetListResult']['Domain'];
                    $dataToLoopThrough = array_map(function ($bits) {
                        return $bits['@attributes'];
                    }, $domains);
                    return array_map(function ($domain) {
                        return (new Transformer)->modify(Domain::class, $domain);
                    }, $dataToLoopThrough);
                });
    }

    /**
     * @return array
     */
    public function check() {
        return $this->request('get', 'domains.check', [], function ($response) {
                    return $response;
                });
    }

    /**
     * @return array
     */
    public function renew() {
        return $this->request('get', 'domains.renew', [], function ($response) {
                    return $response;
                });
    }

    /**
     * @return Nameservers
     */
    public function ns() {
        return new Nameservers($this->config, $this->_client);
    }

}
