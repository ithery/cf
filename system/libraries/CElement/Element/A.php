<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Element_A extends CElement_Element {
    protected $href;

    protected $target;

    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'a';
        $this->href = '';
        $this->target = '';
    }

    /**
     * Set href attribute.
     *
     * @param string $href
     *
     * @return $this
     *
     * @deprecated 1.2 use setHref
     */
    // @codingStandardsIgnoreStart
    public function set_href($href) {
        // @codingStandardsIgnoreEnd
        $this->href = $href;

        return $this;
    }

    /**
     * Set href attribute.
     *
     * @param string $href
     *
     * @return $this
     */
    public function setHref($href) {
        $this->href = $href;

        return $this;
    }

    public function setTarget($target = '_blank') {
        $this->target = $target;

        return $this;
    }

    public function build($indent = 0) {
        if (strlen($this->href) > 0) {
            $this->addAttr('href', $this->href);
        }
        if ($this->target) {
            $this->addAttr('target', $this->target);
        }
    }
}
