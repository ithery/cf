<?php

use Carbon\Carbon;

class CBackup_HouseKeeping_Period {
    /**
     * @var \Carbon\Carbon
     */
    protected $startDate;

    /**
     * @var \Carbon\Carbon
     */
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
