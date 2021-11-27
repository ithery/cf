<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 16, 2019, 2:40:46 AM
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
        $controller = CFRouter::$controller;
        $method = CFRouter::$method;

        if ($nav) {
            $navName = $nav['name'];
            $navLabel = $nav['label'];
            if (isset($nav['action'])) {
                foreach ($nav['action'] as $act) {
                    if (isset($act['controller'], $act['method']) && $act['controller'] == $controller && $act['method'] == $method) {
                        $actionName = $act['name'];
                        $actionLabel = $act['label'];
                    }
                }
            }
        }
        $appId = Base::appId();
        $orgId = Base::orgId();
        $userId = Base::userId();
        $model->fill([
            'org_id' => $orgId,
            'app_id' => $appId,
            'session_id' => CSession::instance()->id(),
            'remote_addr' => CHTTP::request()->ip(),
            'user_agent' => CHTTP::request()->userAgent(),
            'browser' => CApp::browserName(),
            'browser_version' => CApp::browserVersion(),
            'platform' => CApp::platformName(),
            'platform_version' => CApp::platformVersion(),
            'user_id' => $userId,
            'uri' => CFRouter::getCompleteUri(),
            'routed_uri' => crouter::routed_uri(),
            'controller' => CFRouter::getController(),
            'method' => CFRouter::getControllerMethod(),
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
