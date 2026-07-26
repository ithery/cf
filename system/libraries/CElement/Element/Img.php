<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Element_Img extends CElement_Element {
    use CTrait_Compat_Element_Img;

    /**
     * @var null|string
     */
    protected $progressiveImage = null;

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id = '') {
        parent::__construct($id);
        $this->isOneTag = true;
        $this->tag = 'img';
    }

    /**
     * @param string $id
     *
     * @return CElement_Element_Img
     */
    public static function factory($id = '') {
        return new CElement_Element_Img($id);
    }

    /**
     * Set Attribute src.
     *
     * @param string $src
     *
     * @return $this
     */
    public function setSrc($src) {
        $this->setAttr('src', $src);

        return $this;
    }

    /**
     * Set Attribute alt.
     *
     * @param string $alt
     *
     * @return $this
     */
    public function setAlt($alt) {
        $this->setAttr('alt', $alt);

        return $this;
    }
}
