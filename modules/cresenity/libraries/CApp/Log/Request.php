<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 6:13:15 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CApp_Base as Base;

class CApp_Log_Request {

    public static function populate($data = array()) {
        $app = CApp::instance();
        $db = CDatabase::instance();
        $appId = Base::appId();
        $orgId = Base::orgId();
        $userId = Base::userId();
        $browser = new CBrowser();

        //no need to log ajax request
        if (CApp::isAjax()) {
            return false;
        }
        //no need to log on administrator page
        $router_uri = CFRouter::routed_uri(CFRouter::$current_uri);
        $rsegment = explode('/', $router_uri);
        if (count($rsegment) > 0) {
            if ($rsegment[0] == "admin") {
                return false;
            }
        }



        $nav = cnav::nav();

        $nav_name = "";
        $nav_label = "";
        $action_label = "";
        $action_name = "";
        $controller = crouter::controller();
        if ($controller == "cresenity") {
            return false;
        }
        $method = crouter::method();
        if ($nav != null) {
            $nav_name = $nav["name"];
            $nav_label = $nav["label"];

            if (isset($nav["action"])) {
                foreach ($nav["action"] as $act) {
                    if (isset($act["controller"]) && isset($act["method"]) && $act["controller"] == $controller && $act["method"] == $method) {
                        $action_name = $act["name"];
                        $action_label = $act["label"];
                    }
                }
            }
        }
        $db = CDatabase::instance();
        $ip_address = Base::remoteAddress();
        $session_id = CSession::instance()->id();

        $platform_version = crequest::platform_version();
        $description = CF::domain();

        $data = array(
            "request_date" => date("Y-m-d H:i:s"),
            "org_id" => $orgId,
            "session_id" => CSession::instance()->id(),
            "user_agent" => $browser->getUserAgent(),
            "browser" => $browser->getBrowser(),
            "browser_version" => $browser->getVersion(),
            "platform" => $browser->getPlatform(),
            "platform_version" => $platform_version,
            "remote_addr" => $ip_address,
            "user_id" => $userId,
            "uri" => crouter::complete_uri(),
            "routed_uri" => crouter::routed_uri(),
            "controller" => crouter::controller(),
            "method" => crouter::method(),
            "query_string" => crouter::query_string(),
            "nav" => $nav_name,
            "nav_label" => $nav_label,
            "action" => $action_name,
            "action_label" => $action_label,
            "description" => $description,
            "app_id" => $appId,
        );
        $db->insert("log_request", $data);
    }

}
