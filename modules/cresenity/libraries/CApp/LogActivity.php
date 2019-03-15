<?php

use CApp_Model_Observer_LogActivity as Observer;

/**
 * 
 */
class CApp_LogActivity {

    private static $instance;
    private $isStarted;
    private $model;
    private $observer;
    private $message;

    private function __construct() {
        $this->isStarted = false;
        $this->model = null;
        $this->observer = Observer::class;
    }

    /**
     * 
     * @return CApp_LogActivity
     */
    public static function instance() {
        if (!static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function start($message, $listener = null) {
      
        if($listener==null) {
            $listener = array($this,'onActivity');
        }
        $userId = CApp_Base::userId();
        $activity = CModel_Activity::instance();
        $activity->setMessage($message);
        $activity->setListener($listener);
        $activity->start();
    }

    public function stop() {
        $activity = CModel_Activity::instance();
        $activity->stop();
    }
    
    public function onActivity($message,$data) {
        $model = new CApp_Model_LogActivity();
        $nav = cnav::nav();
        $navName = '';
        $navLabel = '';
        $actionName = '';
        $actionLabel = '';
        if (! $nav) {
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

        $model->fill([
            'org_id' => CF::orgId(),
            'app_id' => CF::appId(),
            'session_id' => CSession::instance()->id(),
            'remote_addr' => crequest::remote_address(),
            'user_agent' => CF::user_agent(),
            'platform_version' => crequest::platform_version(),
            'platform' => crequest::platform(),
            'browser_version' => crequest::browser_version(),
            'browser' => crequest::browser(),
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
     
        $model->data=json_encode($data);
        $model->user_id=CApp_Base::userId();
        
        $model->activity_date = CApp_Base::now();
        $model->description = $message;
        $model->save();

     
    }
}
