<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 4:23:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CDetector_Crawler_AbstractProvider {

    /**
     * The data set.
     * 
     * @var array
     */
    protected $data;

    /**
     * Return the data set.
     * 
     * @return array
     */
    public function getAll() {
        return $this->data;
    }

}
