<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 16, 2019, 2:40:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CApp_Base as Base;

class CApp_Log_Activity {

    public static function populate($description, $data) {
        $model = new CApp_Model_LogActivity();
        $nav = cnav::nav();
        $browser = new CBrowser();
        $navName = '';
        $navLabel = '';
        $actionName = '';
        $actionLabel = '';
        if (!$nav) {
            $navName = $nav["name"];
            $navLabel = $nav["label"];
            if (isset($nav["action"])) {
                foreach ($nav["action"] as $act) {
                    if (isset($act["controller"]) && isset($act["method"]) && $act["controller"] == $controller && $act["method"] == $method) {
                        $actionName = $act["name"];
                        $actionLabel = $act["label"];
                    }
                }
            }
        }
        $appId = Base::appId();
        $orgId = Base::orgId();
        $userId = Base::userId();
        $browser = new CBrowser();
        $model->fill([
            'org_id' => $orgId,
            'app_id' => $appId,
            'session_id' => CSession::instance()->id(),
            'remote_addr' => crequest::remote_address(),
            'user_agent' => $browser->getUserAgent(),
            'browser' => $browser->getBrowser(),
            'browser_version' => $browser->getVersion(),
            'platform' => $browser->getPlatform(),
            'platform_version' => null,
            "user_id" => $userId,
            'uri' => crouter::complete_uri(),
            'routed_uri' => crouter::routed_uri(),
            'controller' => crouter::controller(),
            'method' => crouter::method(),
            'query_string' => crouter::query_string(),
            'nav' => $navName,
            'nav_label' => $navLabel,
            'action' => $actionName,
            'action_label' => $actionLabel,
        ]);

        $model->data = json_encode($data);

        $model->activity_date = CApp_Base::now();
        $model->description = $description;
        $model->save();
    }

}
