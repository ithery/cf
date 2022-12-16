<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Oct 28, 2017, 2:25:21 PM
 */
class CElement_Element_Span extends CElement_Element {
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'span';
    }

    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    // @codingStandardsIgnoreStart
    public function set_col($col = null) {
        // @codingStandardsIgnoreEnd
        return $this;
    }
}
