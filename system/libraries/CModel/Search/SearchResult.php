<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 28, 2019, 9:37:29 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Search_SearchResult {

    /** @var \Spatie\Searchable\Searchable */
    public $searchable;

    /** @var string */
    public $title;

    /** @var null|string */
    public $url;

    /** @var string */
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
