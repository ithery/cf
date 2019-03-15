<?php

/**
 * 
 */
class CModel_Activity_Logger
{
    protected $activity;

    private function __construct($activity)
    {
        $this->activity = $activity;
        $this->autoFill();
    }

    public static function activity($activity)
    {
        return new static($activity);
    }

    private function autoFill()
    {
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

        $this->activity->fill([
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
    }

    public function user($userId)
    {
        $this->activity->user_id = $userId;
        return $this;
    }

    public function before(array $data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $this->activity->data_before = $data;
        return $this;
    }

    public function after(array $data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $this->activity->data_after = $data;
        return $this;
    }

    public function changes(array $data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $this->activity->data_changed = $data;
        return $this;
    }

    public function type($type)
    {
        $this->activity->activity_type = $type;
        return $this;
    }

    public function log(string $description)
    {
        $this->activity->activity_date = date('Y-m-d H:i:s');
        $this->activity->description = $description;
        $this->activity->save();

        return $this->activity;
    }
}
