<?php

class CConsole_Prompt_DataTablePrompt extends CConsole_Prompt {
    use CConsole_Prompt_Concerns_Scrolling;
    use CConsole_Prompt_Concerns_TypedValue;

    /**
     * The table headers.
     *
     * @var array<int, array<int, string>|string>
     */
    public $headers;

    /**
     * The table rows.
     *
     * @var array<int|string, array<int, string>>
     */
    public $rows;

    /**
     * The cached filtered rows.
     *
     * @var null|array<int|string, array<int, string>>
     */
    protected $filteredCache;

    /**
     * The previous search query (for cache invalidation).
     *
     * @var string
     */
    protected $previousQuery = '';

    /**
     * @var int
     */
    public $scroll;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $hint;

    /**
     * @var null|Closure
     */
    public $filter;

    /**
     * Create a new DataTable instance.
     *
     * @param array<int, array<int, string>|string>|CCollection      $headers
     * @param null|array<int|string, array<int, string>>|CCollection $rows
     * @param int                                                     $scroll
     * @param string                                                  $label
     * @param string                                                  $hint
     * @param bool|string                                             $required
     * @param mixed                                                   $validate
     * @param null|Closure                                            $transform
     * @param null|Closure                                            $filter
     */
    public function __construct($headers = [], $rows = null, $scroll = 10, $label = '', $hint = '', $required = false, $validate = null, $transform = null, $filter = null) {
        $this->scroll = $scroll;
        $this->label = $label;
        $this->hint = $hint;
        $this->required = $required;
        $this->validate = $validate;
        $this->transform = $transform;
        $this->filter = $filter;

        if ($rows === null) {
            $rows = $headers;
            $headers = [];
        }

        $this->headers = $headers instanceof CCollection ? $headers->all() : $headers;
        $this->rows = $rows instanceof CCollection ? $rows->all() : $rows;

        $this->initializeScrolling(0);

        $ignore = function ($key) {
            return $this->state !== 'search';
        };

        $this->trackTypedValue('', false, $ignore);

        $this->on('key', function ($key) {
            if ($this->state === 'search') {
                $this->handleSearchKey($key);
            } else {
                $this->handleBrowseKey($key);
            }
        });
    }

    /**
     * Handle key presses in browse mode.
     *
     * @param string $key
     *
     * @return void
     */
    protected function handleBrowseKey($key) {
        $total = count($this->filteredRows());

        if (in_array($key, [CConsole_Prompt_Key::UP, CConsole_Prompt_Key::UP_ARROW, CConsole_Prompt_Key::CTRL_P])) {
            $this->highlightPrevious($total);
        } elseif (in_array($key, [CConsole_Prompt_Key::DOWN, CConsole_Prompt_Key::DOWN_ARROW, CConsole_Prompt_Key::CTRL_N])) {
            $this->highlightNext($total);
        } elseif ($key === CConsole_Prompt_Key::PAGE_UP) {
            $this->highlight(max(0, $this->highlighted - $this->scroll));
        } elseif ($key === CConsole_Prompt_Key::PAGE_DOWN) {
            $this->highlight(min($total - 1, $this->highlighted + $this->scroll));
        } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::HOME, CConsole_Prompt_Key::CTRL_A], $key) !== null) {
            $this->highlight(0);
        } elseif (CConsole_Prompt_Key::oneOf([CConsole_Prompt_Key::END, CConsole_Prompt_Key::CTRL_E], $key) !== null) {
            $this->highlight(max(0, $total - 1));
        } elseif ($key === CConsole_Prompt_Key::ENTER) {
            if ($total > 0) {
                $this->submit();
            }
        } elseif ($key === '/') {
            $this->enterSearch();
        }
    }

    /**
     * Handle key presses in search mode.
     *
     * @param string $key
     *
     * @return void
     */
    protected function handleSearchKey($key) {
        if ($key === CConsole_Prompt_Key::ENTER) {
            $this->exitSearch();
        } elseif ($key === CConsole_Prompt_Key::ESCAPE) {
            $this->cancelSearch();
        } else {
            $this->search();
        }
    }

    /**
     * Enter search mode.
     *
     * @return void
     */
    protected function enterSearch() {
        $this->state = 'search';
        $this->typedValue = '';
        $this->cursorPosition = 0;
    }

    /**
     * Exit search mode, keeping the filtered results.
     *
     * @return void
     */
    protected function exitSearch() {
        $this->state = 'active';
        $this->highlighted = 0;
        $this->firstVisible = 0;
    }

    /**
     * Cancel search, clearing the query and showing all rows.
     *
     * @return void
     */
    protected function cancelSearch() {
        $this->state = 'active';
        $this->typedValue = '';
        $this->cursorPosition = 0;
        $this->filteredCache = null;
        $this->previousQuery = '';
        $this->highlighted = 0;
        $this->firstVisible = 0;
    }

    /**
     * Handle typing in search mode.
     *
     * @return void
     */
    protected function search() {
        $this->filteredCache = null;
        $this->highlighted = 0;
        $this->firstVisible = 0;
    }

    /**
     * Get the filtered rows based on the current search query.
     *
     * @return array<int|string, array<int, string>>
     */
    public function filteredRows() {
        if ($this->filteredCache !== null && $this->previousQuery === $this->typedValue) {
            return $this->filteredCache;
        }

        $this->previousQuery = $this->typedValue;

        if ($this->typedValue === '') {
            return $this->filteredCache = $this->rows;
        }

        if ($this->filter !== null) {
            $filter = $this->filter;

            return $this->filteredCache = array_filter(
                $this->rows,
                function ($row) use ($filter) {
                    return call_user_func($filter, $row, $this->typedValue);
                }
            );
        }

        return $this->filteredCache = array_filter(
            $this->rows,
            function ($row) {
                return cstr::contains(
                    mb_strtolower(implode(' ', $row)),
                    mb_strtolower($this->typedValue)
                );
            }
        );
    }

    /**
     * The currently visible rows.
     *
     * @return array<int|string, array<int, string>>
     */
    public function visible() {
        return array_slice($this->filteredRows(), $this->firstVisible, $this->scroll, true);
    }

    /**
     * Get the current search query.
     *
     * @return string
     */
    public function searchValue() {
        return $this->typedValue;
    }

    /**
     * Get the search query with a virtual cursor.
     *
     * @param int $maxWidth
     *
     * @return string
     */
    public function searchWithCursor($maxWidth) {
        if ($this->typedValue === '') {
            return $this->dim($this->addCursor('', 0, $maxWidth));
        }

        return $this->addCursor($this->typedValue, $this->cursorPosition, $maxWidth);
    }

    /**
     * Get the value of the prompt.
     *
     * @return mixed
     */
    public function value() {
        if ($this->highlighted === null) {
            return null;
        }

        $filtered = $this->filteredRows();
        $keys = array_keys($filtered);

        if (!isset($keys[$this->highlighted])) {
            return null;
        }

        return $keys[$this->highlighted];
    }

    /**
     * Get the selected row for display purposes.
     *
     * @return null|array<int, string>
     */
    public function selectedRow() {
        if ($this->highlighted === null) {
            return null;
        }

        $filtered = $this->filteredRows();
        $keys = array_keys($filtered);

        if (!isset($keys[$this->highlighted])) {
            return null;
        }

        return $filtered[$keys[$this->highlighted]];
    }
}
