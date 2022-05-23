<?php

abstract class CCron_Job {
    protected $schedule = '* * * * *';
    protected $name = null;

    public static function instance() {
        $calledClass = get_called_class();
        return new $calledClass();
    }

    abstract public function execute();

    public function getSchedule() {
        return $this->schedule;
    }

    public function getName() {
        $name = $this->name;
        if ($name == null) {
            $name = str_replace(get_class() . '_', '', get_called_class());
        }

        return $name;
    }
}
