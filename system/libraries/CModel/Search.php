<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 28, 2019, 9:31:19 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Search {

    protected $aspects = [];

    /**
     * @param CModel_Search_SearchAspect $searchAspect
     *
     * @return CModel_Search
     */
    public function registerAspect(CModel_Search_SearchAspect$searchAspect) {

        $this->aspects[$searchAspect->getType()] = $searchAspect;
        return $this;
    }

    public function registerModel($modelClass, $attributes, $callback=null) {
        if (isset($attributes[0]) && is_callable($attributes[0])) {
            $attributes = $attributes[0];
        }
        if (is_array(carr::get($attributes, 0))) {
            $attributes = $attributes[0];
        }
        $searchAspect = new CModel_Search_ModelSearchAspect($modelClass, $attributes, $callback);
        $this->registerAspect($searchAspect);
        return $this;
    }

    public function getSearchAspects() {
        return $this->aspects;
    }

    public function search($query, $user = null) {
        return $this->perform($query, $user);
    }

    public function perform($query, $user = null) {
        $searchResults = new CModel_Search_SearchResultCollection();
        CF::collect($this->getSearchAspects())
                ->each(function (CModel_Search_SearchAspect $aspect) use ($query, $user, $searchResults) {
                    $searchResults->addResults($aspect->getType(), $aspect->getResults($query, $user));
                });
        return $searchResults;
    }

}
