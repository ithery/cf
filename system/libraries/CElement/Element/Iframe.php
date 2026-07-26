<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Element_Iframe extends CElement_Element {
    use CTrait_Element_Property_Width,
        CTrait_Element_Property_Height;

    /**
     * @var string
     */
    protected $src = '';

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'iframe';
    }

    // @codingStandardsIgnoreStart
    /**
     * @param string $src
     *
     * @return $this
     */
    public function set_src($src) {
        // @codingStandardsIgnoreEnd
        $this->src = $src;

        return $this;
    }

    /**
     * @param string $src
     *
     * @return $this
     */
    public function setSrc($src) {
        $this->src = $src;

        return $this;
    }

    /**
     * @return void
     */
    public function build() {
        $this->setAttr('src', $this->src);
        if ($this->width) {
            $this->setAttr('width', $this->width);
        }
        if ($this->height) {
            $this->setAttr('height', $this->height);
        }
    }
}
