<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 15, 2019, 6:54:16 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CModel_Activity_Logger as Logger;

/**
 * 
 */
class CApp_Model_Observer_LogActivityObserver implements CModel_Activity_ObserverInterface {

    private $isStarted;
    private $userId;
    private $message;
    private $logActivity;

    public function start($userId, $message, CModel $logActivity) {
        $this->isStarted = true;
        $this->usedId = $userId;
        $this->message = $message;
        $this->logActivity = Logger::activity($logActivity);
    }

    public function stop() {
        $this->isStarted = false;
    }

    public function isStarted() {
        return $this->isStarted;
    }

    public function created(CModel $model) {
        if ($this->isStarted()) {
            $before = [];
            $after = $model->getAttributes();
            $changes = [];

            $this->logActivity
                    ->user($this->userId)
                    ->type('create')
                    ->before($before)
                    ->after($after)
                    ->changes($changes)
                    ->log($this->message);
        }
    }

    public function updated(CModel $model) {
        if ($this->isStarted()) {
            $before = [];
            $after = $model->getAttributes();
            $changes = $model->getDirty();

            foreach ($after as $key => $value) {
                $before[$key] = $model->getOriginal($key);
            }

            $this->logActivity
                    ->user($this->userId)
                    ->type('update')
                    ->before($before)
                    ->after($after)
                    ->changes($changes)
                    ->log($this->message);
        }
    }

    public function deleted(CModel $model) {
        if ($this->isStarted()) {
            $before = [];
            $after = $model->getAttributes();
            $changes = $model->getDirty();

            foreach ($after as $key => $value) {
                $before[$key] = $model->getOriginal($key);
            }

            $this->logActivity
                    ->user($this->userId)
                    ->type('delete')
                    ->before($before)
                    ->after($after)
                    ->changes($changes)
                    ->log($this->message);
        }
    }

}
