<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 14, 2018, 7:46:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Database_Console extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Database Console"));
        $app->manager()->registerModule('terminal');

        $widget = $app->addWidget();
        
        $terminal = $widget->addTerminal();
        $terminal->setAjaxUrl(curl::base().'administrator/database/console/rpc');

        echo $app->render();
    }

    public function rpc() {
        $db = CDatabaseRPC::factory();
		handle_json_rpc($db);
    }
}

// End Home Controller