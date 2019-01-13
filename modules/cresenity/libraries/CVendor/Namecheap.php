<?php

defined('SYSPATH') OR die('No direct access allowed.');

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * @author Hery Kurniawan
 * @since Oct 25, 2018, 5:14:37 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CVendor_Namecheap {

    /**
     * @const string
     */
    const ENV_SANDBOX = 'https://api.sandbox.namecheap.com/';

    /**
     * @const string
     */
    const ENV_PRODUCTION = 'https://api.namecheap.com/';

    /**
     * @var CVendor_Namecheap_Config
     */
    protected $config;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Api constructor.
     * @param array $options
     * @param ClientInterface $client
     */
    public function __construct($options) {
        $apiUser = carr::get($options, 'apiUser');
        $apiKey = carr::get($options, 'apiKey');
        $username = carr::get($options, 'username');
        $clientIp = carr::get($options, 'clientIp');
        $environment = carr::get($options, 'environment', 'sandbox');

        $config = new CVendor_Namecheap_Config($apiUser, $apiKey, $username, $clientIp);
        $this->config = $config;


        $baseUri = self::ENV_PRODUCTION;
        if ($environment == 'sandbox') {
            $baseUri = self::ENV_SANDBOX;
        }
        $client = new Client([
            'base_uri' => $baseUri,
        ]);


        $this->client = $client;
    }

    /**
     * @return Domains
     */
    public function domains() {
        return new CVendor_Namecheap_Interaction_Domains($this->config, $this->client);
    }

}
