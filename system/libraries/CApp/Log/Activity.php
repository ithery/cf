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
        $controller = '';
        $method = '';
        $queryString = '';
        $routedUri = '';
        $completeUri = '';
        $request = c::request();
        $route = $request->route();
        if ($route) {
            /** @var CRouting_Route $route */
            $routeData = $route->getRouteData();
            if ($routeData) {
                $controller = $routeData->getControllerClass();
                $method = $routeData->getMethod();
                $queryString = $routeData->getQueryString();
                $routedUri = $routeData->getRoutedUri();
                $completeUri = $routeData->getCompleteUri();
            }
        }

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
        $username = Base::username();
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
            'uri' => $completeUri,
            'routed_uri' => $routedUri,
            'controller' => $controller,
            'method' => $method,
            'query_string' => $queryString,
            'nav' => $navName,
            'nav_label' => $navLabel,
            'action' => $actionName,
            'action_label' => $actionLabel,
            'createdby' => $username,
        ]);
        $data = static::normalizeDataForJsonEncoding($data);
        $model->data = json_encode($data);

        $model->activity_date = c::now();
        $model->description = $description;
        $model->save();
    }

    protected static function normalizeDataForJsonEncoding($data) {
        foreach ($data as $dataIndex => $record) {
            $beforeData = carr::get($record, 'before');
            $afterData = carr::get($record, 'after');
            foreach ($beforeData as $beforeIndex => $value) {
                if ($value instanceof CCarbon || $value instanceof CarbonV3\Carbon || $value instanceof CarbonLegacy\Carbon) {
                    $data[$dataIndex]['before'][$beforeIndex] = (string) $value;
                }
            }
            foreach ($afterData as $afterIndex => $value) {
                if ($value instanceof CCarbon || $value instanceof CarbonV3\Carbon || $value instanceof CarbonLegacy\Carbon) {
                    $data[$dataIndex]['after'][$afterIndex] = (string) $value;
                }
            }
        }

        return $data;
    }
}
