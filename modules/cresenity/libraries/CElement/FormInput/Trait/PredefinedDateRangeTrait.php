<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2019, 7:38:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Carbon\Carbon;

trait CElement_FormInput_Trait_PredefinedDateRangeTrait {

    protected $predefinedRanges;

    public function resetRange() {
        $this->predefinedRanges = array();
        return $this;
    }

    public function addRange($label, $dateStart, $dateEnd) {
        $this->predefinedRanges[] = array(
            "label" => $label,
            "dateStart" => $dateStart,
            "dateEnd" => $dateEnd,
        );
        return $this;
    }

    public function addRangeLifeTime($label = 'Life Time') {
        $dateStart =  Carbon::createFromTimestamp(0);
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }
    public function addRangeToday($label = 'Today') {
        $dateStart = Carbon::now();
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }

    public function addRangeYesterday($label = 'Yesterday') {
        $dateStart = Carbon::yesterday()->hour(0)->minute(0)->second(0);
        $dateEnd = Carbon::yesterday()->hour(23)->minute(59)->second(59);
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }

    public function addRange7Days($label = 'Last 7 Days') {
        $dateStart = Carbon::now()->subDay(7);
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }

    public function addRange14Days($label = 'Last 14 Days') {
        $dateStart = Carbon::now()->subDay(14);
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }

    public function addRange30Days($label = 'Last 30 Days') {
        $dateStart = Carbon::now()->subDay(30);
        $dateEnd = Carbon::now();
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }

    public function addRangeThisWeek($label = 'This Week') {
        $dateStart = Carbon::now()->modify('this week');
        $dateEnd = Carbon::now()->modify('this week +6 days');
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }

    public function addRangeLastWeek($label = 'Last Week') {
        $dateStart = Carbon::now()->modify('last week');
        $dateEnd = Carbon::now()->modify('last week +6 days');
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }
    
    public function addRangeThisMonth($label = 'This Month') {
        $dateStart = Carbon::now()->modify('first day of this month');
        $dateEnd = Carbon::now()->modify('last day of this month');
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }

    public function addRangeLastMonth($label = 'Last Month') {
        $dateStart = Carbon::now()->modify('first day of last month');
        $dateEnd = Carbon::now()->modify('last day of last month');
        $this->addRange($label, $dateStart->format($this->dateFormat), $dateEnd->format($this->dateFormat));
        return $this;
    }

    public function addDefaultRange() {
        $this->resetRange();
        $this->addRangeLifeTime();
        $this->addRangeToday();
        $this->addRangeYesterday();
        $this->addRange7Days();
        $this->addRange14Days();
        $this->addRange30Days();
        $this->addRangeThisWeek();
        $this->addRangeLastWeek();
        $this->addRangeThisMonth();
        $this->addRangeLastMonth();
        return $this;
    }

}
