<?php
class CWebsocketClientSession {
    /** @var integer session's id */
    private $id;
    /** @var integer session's last heartbeat */
    private $heartbeat;
    /** @var integer[] session's and heartbeat's timeouts */
    private $timeouts;
    /** @var string[] supported upgrades */
    private $upgrades;
    public function __construct($id, $interval, $timeout, array $upgrades)
    {
        $this->id        = $id;
        $this->upgrades  = $upgrades;
        $this->heartbeat = time();
        $this->timeouts  = array('timeout'  => $timeout, 'interval' => $interval);
    }
    /** The property should not be modified, hence the private accessibility on them */
    public function __get($prop) {
        static $list = array('id', 'upgrades');
        if (!in_array($prop, $list)) {
            throw new Exception(sprintf('Unknown property "%s" for the Session object. Only the following are availables : ["%s"]', $prop, implode('", "', $list)));
        }
        return $this->$prop;
    }
    /**
     * Checks whether a new heartbeat is necessary, and does a new heartbeat if it is the case
     *
     * @return Boolean true if there was a heartbeat, false otherwise
     */
    public function needs_heartbeat()
    {
        if (0 < $this->timeouts['interval'] && time() > ($this->timeouts['interval'] + $this->heartbeat - 5)) {
            $this->heartbeat = time();
            return true;
        }
        return false;
    }
}
