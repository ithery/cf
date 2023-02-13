<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 24, 2019, 12:41:06 PM
 */
class CElement_Element_Img extends CElement_Element {
    use CTrait_Compat_Element_Img;

    protected $progressiveImage = null;

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
     */
    public function setSrc($src) {
        $this->setAttr('src', $src);

        return $this;
    }

    /**
     * Set Attribute alt.
     *
     * @param string $alt
     */
    public function setAlt($alt) {
        $this->setAttr('alt', $alt);

        return $this;
    }
}
