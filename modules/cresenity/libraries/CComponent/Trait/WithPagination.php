<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 30, 2020 
 * @license Ittron Global Teknologi
 */
trait CComponent_Trait_WithPagination {

    public $page = 1;
    

    public function getQueryString() {
        return array_merge(['page' => ['except' => 1]], $this->queryString);
    }

    public function initializeWithPagination() {
        $this->page = $this->resolvePage();

        CPagination_Paginator::currentPageResolver(function () {
            return $this->page;
        });

        CPagination_Paginator::defaultView($this->paginationView());
        CPagination_Paginator::defaultSimpleView($this->paginationSimpleView());
    }

    public function paginationView() {
        return 'pagination.component.' . (property_exists($this, 'paginationTheme') ? $this->paginationTheme : 'bootstrap');
    }

    public function paginationSimpleView() {
        return 'pagination.component.simple-' . (property_exists($this, 'paginationTheme') ? $this->paginationTheme : 'bootstrap');
    }

    public function previousPage() {
        $this->setPage(max($this->page - 1, 1));
    }

    public function nextPage() {
        $this->setPage($this->page + 1);
    }

    public function gotoPage($page) {
        $this->setPage($page);
    }

    public function resetPage() {
        $this->setPage(1);
    }

    public function setPage($page) {
        $this->page = $page;
    }

    public function resolvePage() {
        // The "page" query string item should only be available
        // from within the original component mount run.
        return CHTTP::request()->query('page', $this->page);
    }

}
