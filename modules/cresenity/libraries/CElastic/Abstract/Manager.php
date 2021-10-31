<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 11:30:42 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class AbstractManager {

    /**
     * @var CElastic
     */
    protected $elastic;

    /**
     * @param CElastic $elasticSearcher
     */
    public function __construct(CElastic $elastic) {
        $this->elastic = $elastic;
    }

}
