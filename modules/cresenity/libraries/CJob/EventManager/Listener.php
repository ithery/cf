<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 19, 2018, 12:01:26 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJob_EventManager_Listener {

    public function onJobPreRun(CJob_EventManager_Args $event) {
        $callback = CJob_EventManager::getCallback(CJob_Events::onJobPreRun);
        $this->runCallback($callback, $event);
    }

    public function onJobPostRun(CJob_EventManager_Args $event) {
        $callback = CJob_EventManager::getCallback(CJob_Events::onJobPostRun);
        $this->runCallback($callback, $event);
    }

    public function onBackgroundJobPreRun(CJob_EventManager_Args $event) {
        $callback = CJob_EventManager::getCallback(CJob_Events::onBackgroundJobPreRun);
        $this->runCallback($callback, $event);
    }

    public function onBackgroundJobPostRun(CJob_EventManager_Args $event) {
        $callback = CJob_EventManager::getCallback(CJob_Events::onBackgroundJobPostRun);
        $this->runCallback($callback, $event);
    }

    protected function runCallback($callback, $event) {
        if ($callback != null) {
            $callbacks = $callback;
            if (!is_array($callbacks)) {
                $callbacks = array($callbacks);
            }
            foreach ($callbacks as $callback) {
                call_user_func($callback, $event);
            }
        }
    }

}
