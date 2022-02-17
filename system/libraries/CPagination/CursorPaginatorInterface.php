<?php

interface CPagination_CursorPaginatorInterface {
    /**
     * Get the URL for a given cursor.
     *
     * @param null|\CPagination_Cursor $cursor
     *
     * @return string
     */
    public function url($cursor);

    /**
     * Add a set of query string values to the paginator.
     *
     * @param null|array|string $key
     * @param null|string       $value
     *
     * @return $this
     */
    public function appends($key, $value = null);

    /**
     * Get / set the URL fragment to be appended to URLs.
     *
     * @param null|string $fragment
     *
     * @return null|$this|string
     */
    public function fragment($fragment = null);

    /**
     * Get the URL for the previous page, or null.
     *
     * @return null|string
     */
    public function previousPageUrl();

    /**
     * The URL for the next page, or null.
     *
     * @return null|string
     */
    public function nextPageUrl();

    /**
     * Get all of the items being paginated.
     *
     * @return array
     */
    public function items();

    /**
     * Get the "cursor" of the previous set of items.
     *
     * @return null|\CPagination_Cursor
     */
    public function previousCursor();

    /**
     * Get the "cursor" of the next set of items.
     *
     * @return null|\CPagination_Cursor
     */
    public function nextCursor();

    /**
     * Determine how many items are being shown per page.
     *
     * @return int
     */
    public function perPage();

    /**
     * Get the current cursor being paginated.
     *
     * @return null|\CPagination_Cursor
     */
    public function cursor();

    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages();

    /**
     * Get the base path for paginator generated URLs.
     *
     * @return null|string
     */
    public function path();

    /**
     * Determine if the list of items is empty or not.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Determine if the list of items is not empty.
     *
     * @return bool
     */
    public function isNotEmpty();

    /**
     * Render the paginator using a given view.
     *
     * @param null|string $view
     * @param array       $data
     *
     * @return string
     */
    public function render($view = null, $data = []);
}
