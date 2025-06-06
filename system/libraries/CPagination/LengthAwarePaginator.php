<?php

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class CPagination_LengthAwarePaginator extends CPagination_AbstractPaginator implements Arrayable, ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Jsonable, CPagination_LengthAwarePaginatorInterface {
    /**
     * The total number of items before slicing.
     *
     * @var int
     */
    protected $total;

    /**
     * The last available page.
     *
     * @var int
     */
    protected $lastPage;

    /**
     * Create a new paginator instance.
     *
     * @param mixed    $items
     * @param int      $total
     * @param int      $perPage
     * @param null|int $currentPage
     * @param array    $options     (path, query, fragment, pageName)
     *
     * @return void
     */
    public function __construct($items, $total, $perPage, $currentPage = null, array $options = []) {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->total = $total;
        $this->perPage = $perPage;
        $this->lastPage = (int) ceil($total / $perPage);
        $this->path = $this->path != '/' ? rtrim($this->path, '/') : $this->path;
        $this->currentPage = $this->setCurrentPage($currentPage, $this->pageName);
        $this->items = $items instanceof CCollection ? $items : CCollection::make($items);
    }

    /**
     * Get the current page for the request.
     *
     * @param int    $currentPage
     * @param string $pageName
     *
     * @return int
     */
    protected function setCurrentPage($currentPage, $pageName) {
        $currentPage = $currentPage ?: static::resolveCurrentPage($pageName);

        return $this->isValidPageNumber($currentPage) ? (int) $currentPage : 1;
    }

    /**
     * Render the paginator using the given view.
     *
     * @param string $view
     * @param array  $data
     *
     * @return string
     */
    public function links($view = null, $data = []) {
        return $this->render($view, $data);
    }

    /**
     * Render the paginator using the given view.
     *
     * @param string $view
     * @param array  $data
     *
     * @return string
     */
    public function render($view = null, $data = []) {
        return new CBase_HtmlString(static::viewFactory()->make($view ?: static::$defaultView, array_merge($data, [
            'paginator' => $this,
            'elements' => $this->elements(),
        ]))->render());
    }

    /**
     * Get the array of elements to pass to the view.
     *
     * @return array
     */
    protected function elements() {
        $window = CPagination_UrlWindow::make($this);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }

    /**
     * Get the total number of items being paginated.
     *
     * @return int
     */
    public function total() {
        return $this->total;
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages() {
        return $this->currentPage() < $this->lastPage();
    }

    /**
     * Get the URL for the next page.
     *
     * @return null|string
     */
    public function nextPageUrl() {
        if ($this->lastPage() > $this->currentPage()) {
            return $this->url($this->currentPage() + 1);
        }
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function lastPage() {
        return $this->lastPage;
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
            'last_page' => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path,
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
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
