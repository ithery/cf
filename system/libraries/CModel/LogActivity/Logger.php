<?php

use CModel_LogActivity_Model as LogActivityModel;

/**
 * 
 */
class CModel_LogActivity_Logger
{
    protected $activity;

    public function __construct()
    {
        $this->activity = new LogActivityModel();
    }

    public function before(array $data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $this->activity->before = $data;
        return $this;
    }

    public function after(array $data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $this->activity->after = $data;
        return $this;
    }

    public function log(string $description)
    {
        $this->activity->description = $description;
        $this->activity->save();

        return $this->activity;
    }

    public static function activity()
    {
        return new static;
    }
}
