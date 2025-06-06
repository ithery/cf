<?php

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class CPagination_CursorPaginator extends CPagination_CursorPaginatorAbstract implements Arrayable, ArrayAccess, Countable, IteratorAggregate, Jsonable, JsonSerializable, CPagination_CursorPaginatorInterface {
    /**
     * Create a new paginator instance.
     *
     * @param mixed                    $items
     * @param int                      $perPage
     * @param null|\CPagination_Cursor $cursor
     * @param array                    $options (path, query, fragment, pageName)
     *
     * @return void
     */
    public function __construct($items, $perPage, $cursor = null, array $options = []) {
        $this->options = $options;

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->perPage = $perPage;
        $this->cursor = $cursor;
        $this->path = $this->path !== '/' ? rtrim($this->path, '/') : $this->path;

        $this->setItems($items);
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

        $this->hasMore = $this->items->count() > $this->perPage;

        $this->items = $this->items->slice(0, $this->perPage);

        if (!is_null($this->cursor) && $this->cursor->pointsToPreviousItems()) {
            $this->items = $this->items->reverse()->values();
        }
    }

    /**
     * Render the paginator using the given view.
     *
     * @param null|string $view
     * @param array       $data
     *
     * @return \CInterface_Htmlable
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
     * @return \CInterface_Htmlable
     */
    public function render($view = null, $data = []) {
        return static::viewFactory()->make($view ?: CPagination_Paginator::$defaultSimpleView, array_merge($data, [
            'paginator' => $this,
        ]));
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages() {
        return (is_null($this->cursor) && $this->hasMore)
            || (!is_null($this->cursor) && $this->cursor->pointsToNextItems() && $this->hasMore)
            || (!is_null($this->cursor) && $this->cursor->pointsToPreviousItems());
    }

    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages() {
        return !$this->onFirstPage() || $this->hasMorePages();
    }

    /**
     * Determine if the paginator is on the first page.
     *
     * @return bool
     */
    public function onFirstPage() {
        return is_null($this->cursor) || ($this->cursor->pointsToPreviousItems() && !$this->hasMore);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'data' => $this->items->toArray(),
            'path' => $this->path(),
            'per_page' => $this->perPage(),
            'next_cursor' => c::optional($this->nextCursor())->encode(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_cursor' => c::optional($this->previousCursor())->encode(),
            'prev_page_url' => $this->previousPageUrl(),
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
