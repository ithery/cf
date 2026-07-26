<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Element_Span extends CElement_Element {
    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'span';
    }

    /**
     * @param null|string $id
     *
     * @return static
     */
    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    // @codingStandardsIgnoreStart
    /**
     * @param mixed $col
     *
     * @return $this
     */
    public function set_col($col = null) {
        // @codingStandardsIgnoreEnd
        return $this;
    }
}
