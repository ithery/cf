<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:31:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model extends CModel {

    public function cache() {
        return new CTracker_Cache();
    }

    public function save(array $options = []) {
        if ($this->org_id == null) {
            if (CApp_Base::orgId() != null) {
                $this->org_id = CApp_Base::orgId();
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

}
