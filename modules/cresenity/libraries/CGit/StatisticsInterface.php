<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 3, 2019, 1:46:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CGit_StatisticsInterface {

    public function addCommit(Commit $commit);

    public function sortCommits();
}
