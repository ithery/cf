<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 28, 2019, 9:56:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Search_SearchResultCollection extends CCollection {

    /**
     * The total number of items before slicing.
     *
     * @var int
     */
    protected $paginator;


    public function addResults($type, $results) {
        
        $this->paginator[$type]=$results;
        
        c::collect($results->items())->each(function ($result) use ($type) {
            
            $this->items[] = $result->getSearchResult()->setType($type);
        });
        return $this;
    }

    public function groupByType() {
        return $this->groupBy(function (CModel_Search_SearchResult $searchResult) {
                    return $searchResult->type;
                });
    }

    public function aspect($aspectName) {
        return $this->groupByType()->get($aspectName);
    }

    public function paginator($type=null) {
        if($type==null) {
            return $this->paginator;
        }
        return carr::get($this->paginator,$type);
    }

}
