<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 12, 2019, 3:23:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDaemon_ServiceAbstract {

    protected $serviceName;
    
    protected $config;
    
    public function __construct($serviceName, array $config) {
        $this->serviceName = $serviceName;
        $this->config = $config + [
        ];
        $this->helper = $helper ?: new CJob_Helper();
        $this->tmpDir = $this->helper->getTempDir();
        CJob_EventManager::initialize();
    }

}
