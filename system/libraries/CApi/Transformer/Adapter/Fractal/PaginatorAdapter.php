<?php

use League\Fractal\Pagination\PaginatorInterface;

class CApi_Transformer_Adapter_Fractal_PaginatorAdapter implements PaginatorInterface {
    /**
     * The paginator instance.
     *
     * @var \CPagination_LengthAwarePaginatorInterface|CPagination_Paginator
     */
    protected $paginator;

    /**
     * Create a new  pagination adapter.
     *
     * @param \CPagination_LengthAwarePaginatorInterface $paginator
     *
     * @return void
     */
    public function __construct(CPagination_LengthAwarePaginatorInterface $paginator) {
        $this->paginator = $paginator;
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function getCurrentPage() {
        return $this->paginator->currentPage();
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function getLastPage() {
        return $this->paginator->lastPage();
    }

    /**
     * Get the total.
     *
     * @return int
     */
    public function getTotal() {
        return $this->paginator->total();
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function getCount() {
        return $this->paginator->count();
    }

    /**
     * Get the number per page.
     *
     * @return int
     */
    public function getPerPage() {
        return $this->paginator->perPage();
    }

    /**
     * Get the url for the given page.
     *
     * @param int $page
     *
     * @return string
     */
    public function getUrl($page) {
        return $this->paginator->url($page);
    }

    /**
     * Get the paginator instance.
     *
     * @return \CPagination_LengthAwarePaginatorInterface
     */
    public function getPaginator() {
        return $this->paginator;
    }
}
