<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 8:56:20 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_Tracker_TrackerTrait {

    public function cache() {
        return new CTracker_Cache();
    }

    public function save(array $options = []) {
        if ($this->org_id == null) {
            if (CApp_Base::orgId() != null) {
                $this->org_id = (int) CApp_Base::orgId();
            }
        }
        parent::save($options);
        $this->cache()->makeKeyAndPut($this, $this->getKeyName());
    }

    public function scopePeriod($query, CPeriod $minutes, $alias = '') {
        $alias = $alias ? "$alias." : '';
        return $query
                        ->where($alias . 'updated', '>=', (string) ($minutes->startDate ? $minutes->startDate : 1))
                        ->where($alias . 'updated', '<=', (string) ($minutes->endDate ? $minutes->endDate : 1));
    }

    public function getConfig() {
        return CTracker::config();
    }

}
