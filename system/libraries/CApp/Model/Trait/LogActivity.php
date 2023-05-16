<?php

/**
 * @property-read int                         $log_activity_id
 * @property      null|int                    $org_id
 * @property      null|int                    $user_id
 * @property      null|int                    $app_id
 * @property      null|string                 $session_id
 * @property      null|string                 $remote_addr
 * @property      null|string                 $user_agent
 * @property      null|string                 $platform_version
 * @property      null|string                 $platform
 * @property      null|string                 $browser_version
 * @property      null|string                 $browser
 * @property      null|string                 $uri
 * @property      null|string                 $routed_uri
 * @property      null|string                 $controller
 * @property      null|string                 $method
 * @property      null|string                 $query_string
 * @property      null|string                 $nav
 * @property      null|string                 $nav_label
 * @property      null|string                 $action
 * @property      null|string                 $action_label
 * @property      null|string                 $description
 * @property      null|string                 $data
 * @property      null|CCarbon|\Carbon\Carbon $activity_date
 * @property      null|CCarbon|\Carbon\Carbon $deleted
 * @property      null|string                 $deletedby
 */
trait CApp_Model_Trait_LogActivity {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'log_activity_id';
        $this->table = 'log_activity';
        $this->guarded = ['log_activity_id'];
    }
}
