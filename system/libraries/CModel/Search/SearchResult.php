<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 28, 2019, 9:37:29 PM
 */
class CModel_Search_SearchResult {
    /**
     * @var CModel_SearchableInterface
     */
    public $searchable;

    /**
     * @var string
     */
    public $title;

    /**
     * @var null|string
     */
    public $url;

    /**
     * @var string
     */
    public $type;

    public function __construct($searchable, $title, $url = null) {
        $this->searchable = $searchable;
        $this->title = $title;
        $this->url = $url;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }
}
