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
        parent::save($options);
        $this->cache()->makeKeyAndPut($this, $this->getKeyName());
    }

    public function scopePeriod($query, $minutes, $alias = '') {
        $alias = $alias ? "$alias." : '';
        return $query
                        ->where($alias . 'updated', '>=', $minutes->getStart() ? $minutes->getStart() : 1)
                        ->where($alias . 'updated', '<=', $minutes->getEnd() ? $minutes->getEnd() : 1);
    }

}
