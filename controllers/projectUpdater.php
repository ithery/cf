<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Nov 11, 2015
     */
// HAhahahaha
    class projectUpdater_Controller extends CB2CController {

        public function __construct() {
            parent::__construct();
        }

        public function index() {
            $updater_client = ProjectUpdaterClient::instance(array('GetProjectFile'));
            $updater_client->exec(null);
            $api_data = $updater_client->get_response();
            echo "<pre>";
            print_r($api_data);
            echo "</pre>";
        }

    }
    
    // Hehehehe