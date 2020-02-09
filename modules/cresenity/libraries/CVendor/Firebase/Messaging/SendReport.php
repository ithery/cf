<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CVendor_Firebase_Messaging_SendReport {

    /** @var MessageTarget */
    private $target;

    /** @var array|null */
    private $result;

    /** @var Throwable|null */
    private $error;

    private function __construct() {
        
    }

    public static function success(CVendor_Firebase_Messaging_MessageTarget $target, $response) {
        $report = new self();
        $report->target = $target;
        $report->result = $response;

        return $report;
    }

    public static function failure(CVendor_Firebase_Messaging_MessageTarget $target, $error) {
        $report = new self();
        $report->target = $target;
        $report->error = $error;

        return $report;
    }

    public function target() {
        return $this->target;
    }

    public function isSuccess() {
        return $this->error === null;
    }

    public function isFailure() {
        return $this->error !== null;
    }

    /**
     * @return array|null
     */
    public function result() {
        return $this->result;
    }

    /**
     * @return Throwable|null
     */
    public function error() {
        return $this->error;
    }

}
