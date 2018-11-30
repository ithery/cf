<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 25, 2018, 5:26:16 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use GuzzleHttp\ClientInterface;

/**
 * Class Interaction
 * @package Kregel\Namecheap\Interactions
 */
abstract class CVendor_Namecheap_Interaction {

    use CVendor_Namecheap_InteractsWithApiTrait;

    /**
     * @var Config
     */
    protected $config;

    /**
     * CVendor_Namecheap_Interaction constructor.
     * @param CVendor_Namecheap_Config $config
     * @param ClientInterface $client
     */
    public function __construct(CVendor_Namecheap_Config $config, ClientInterface $client) {
        $this->config = $config;
        $this->_client = $client;
    }

}
