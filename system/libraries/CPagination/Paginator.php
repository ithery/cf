<?php

use Illuminate\Contracts\Support\Arrayable;

class CPagination_Paginator extends CPagination_AbstractPaginator implements Arrayable, ArrayAccess, Countable, IteratorAggregate, CInterface_Jsonable, JsonSerializable, CPagination_PaginatorInterface {
    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    protected $hasMore;

    /**
     * Create a new paginator instance.
     *
     * @param mixed    $items
     * @param int      $perPage
     * @param null|int $currentPage
     * @param array    $options     (path, query, fragment, pageName)
     *
     * @return void
     */
    public function __construct($items, $perPage, $currentPage = null, array $options = []) {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->perPage = $perPage;
        $this->currentPage = $this->setCurrentPage($currentPage);
        $this->path = $this->path != '/' ? rtrim($this->path, '/') : $this->path;

        $this->setItems($items);
    }

    /**
     * Get the current page for the request.
     *
     * @param int $currentPage
     *
     * @return int
     */
    protected function setCurrentPage($currentPage) {
        $currentPage = $currentPage ?: static::resolveCurrentPage();

        return $this->isValidPageNumber($currentPage) ? (int) $currentPage : 1;
    }

    /**
     * Set the items for the paginator.
     *
     * @param mixed $items
     *
     * @return void
     */
    protected function setItems($items) {
        $this->items = $items instanceof CCollection ? $items : CCollection::make($items);

        $this->hasMore = count($this->items) > ($this->perPage);

        $this->items = $this->items->slice(0, $this->perPage);
    }

    /**
     * Get the URL for the next page.
     *
     * @return null|string
     */
    public function nextPageUrl() {
        if ($this->hasMorePages()) {
            return $this->url($this->currentPage() + 1);
        }
    }

    /**
     * Render the paginator using the given view.
     *
     * @param null|string $view
     * @param array       $data
     *
     * @return string
     */
    public function links($view = null, $data = []) {
        return $this->render($view, $data);
    }

    /**
     * Render the paginator using the given view.
     *
     * @param null|string $view
     * @param array       $data
     *
     * @return string
     */
    public function render($view = null, $data = []) {
        return new CBase_HtmlString(
            static::viewFactory()->make($view ?: static::$defaultSimpleView, array_merge($data, [
                'paginator' => $this,
            ]))->render()
        );
    }

    /**
     * Manually indicate that the paginator does have more pages.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function hasMorePagesWhen($value = true) {
        $this->hasMore = $value;

        return $this;
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages() {
        return $this->hasMore;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'current_page' => $this->currentPage(),
            'data' => $this->items->toArray(),
            'from' => $this->firstItem(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path,
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
        ];
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize() {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this->jsonSerialize(), $options);
    }
}
