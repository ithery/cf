<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Carbon\Carbon;

class CBackup_HouseKeeping_Period {

    /** @var \Carbon\Carbon */
    protected $startDate;

    /** @var \Carbon\Carbon */
    protected $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate) {
        $this->startDate = $startDate;

        $this->endDate = $endDate;
    }

    public function startDate() {
        return $this->startDate->copy();
    }

    public function endDate() {
        return $this->endDate->copy();
    }

}
