<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 3, 2019, 1:46:04 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Aggregate statistics based on day.
 */
class CGit_Statistics_Date extends CCollection implements CGit_StatisticsInterface {

    /**
     * @param Commit $commit
     */
    public function addCommit(Commit $commit) {
        $day = $commit->getCommiterDate()->format('Y-m-d');
        $this->items[$day][] = $commit;
    }

    public function sortCommits() {
        ksort($this->items);
    }

}
