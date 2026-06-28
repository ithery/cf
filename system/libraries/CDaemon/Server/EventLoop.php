<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDaemon_Server_EventLoop {
    /**
     * @var resource[]
     */
    protected $readFds = [];

    /**
     * @var resource[]
     */
    protected $writeFds = [];

    /**
     * @var resource[]
     */
    protected $exceptFds = [];

    /**
     * @var callable[]
     */
    protected $readCallbacks = [];

    /**
     * @var callable[]
     */
    protected $writeCallbacks = [];

    /**
     * @var callable[]
     */
    protected $exceptCallbacks = [];

    /**
     * @var array
     */
    protected $timers = [];

    /**
     * @var int
     */
    protected $timerIdCounter = 0;

    /**
     * @var bool
     */
    protected $running = true;

    /**
     * @param resource $fd
     * @param int      $flag
     * @param callable $func
     *
     * @return bool
     */
    public function add($fd, $flag, $func) {
        $fdKey = (int) $fd;
        switch ($flag) {
            case CDaemon_Server_Constant::EV_READ:
                $this->readFds[$fdKey] = $fd;
                $this->readCallbacks[$fdKey] = $func;
                break;
            case CDaemon_Server_Constant::EV_WRITE:
                $this->writeFds[$fdKey] = $fd;
                $this->writeCallbacks[$fdKey] = $func;
                break;
            case CDaemon_Server_Constant::EV_EXCEPT:
                $this->exceptFds[$fdKey] = $fd;
                $this->exceptCallbacks[$fdKey] = $func;
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * @param resource $fd
     * @param int      $flag
     *
     * @return bool
     */
    public function del($fd, $flag) {
        $fdKey = (int) $fd;
        switch ($flag) {
            case CDaemon_Server_Constant::EV_READ:
                unset($this->readFds[$fdKey], $this->readCallbacks[$fdKey]);
                break;
            case CDaemon_Server_Constant::EV_WRITE:
                unset($this->writeFds[$fdKey], $this->writeCallbacks[$fdKey]);
                break;
            case CDaemon_Server_Constant::EV_EXCEPT:
                unset($this->exceptFds[$fdKey], $this->exceptCallbacks[$fdKey]);
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * @param float    $interval
     * @param callable $func
     * @param array    $args
     * @param bool     $persistent
     *
     * @return int
     */
    public function addTimer($interval, $func, $args = [], $persistent = true) {
        $id = ++$this->timerIdCounter;
        $this->timers[$id] = [
            'interval' => $interval,
            'func' => $func,
            'args' => (array) $args,
            'persistent' => $persistent,
            'next' => microtime(true) + $interval,
        ];

        return $id;
    }

    /**
     * @param int $timerId
     *
     * @return bool
     */
    public function delTimer($timerId) {
        unset($this->timers[$timerId]);

        return true;
    }

    /**
     * @return void
     */
    public function loop() {
        while ($this->running) {
            $this->tick();
        }
    }

    /**
     * @return void
     */
    public function tick() {
        $now = microtime(true);
        foreach ($this->timers as $id => $timer) {
            if ($now >= $timer['next']) {
                call_user_func_array($timer['func'], $timer['args']);
                if ($timer['persistent']) {
                    $this->timers[$id]['next'] = $now + $timer['interval'];
                } else {
                    unset($this->timers[$id]);
                }
            }
        }

        if (empty($this->readFds) && empty($this->writeFds) && empty($this->exceptFds)) {
            usleep(50000);

            return;
        }

        $read = $this->readFds;
        $write = $this->writeFds;
        $except = $this->exceptFds;

        set_error_handler(function () {
        });
        $ret = stream_select($read, $write, $except, 0, 200000);
        restore_error_handler();

        if (!$ret) {
            return;
        }

        foreach ($read as $fd) {
            $fdKey = (int) $fd;
            if (isset($this->readCallbacks[$fdKey])) {
                call_user_func_array($this->readCallbacks[$fdKey], [$fd]);
            }
        }

        foreach ($write as $fd) {
            $fdKey = (int) $fd;
            if (isset($this->writeCallbacks[$fdKey])) {
                call_user_func_array($this->writeCallbacks[$fdKey], [$fd]);
            }
        }

        foreach ($except as $fd) {
            $fdKey = (int) $fd;
            if (isset($this->exceptCallbacks[$fdKey])) {
                call_user_func_array($this->exceptCallbacks[$fdKey], [$fd]);
            }
        }
    }

    /**
     * @return void
     */
    public function stop() {
        $this->running = false;
    }

    /**
     * @return void
     */
    public function destroy() {
        $this->running = false;
        $this->readFds = $this->writeFds = $this->exceptFds = [];
        $this->readCallbacks = $this->writeCallbacks = $this->exceptCallbacks = [];
        $this->timers = [];
    }
}
