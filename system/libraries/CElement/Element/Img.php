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

    public function __construct($id = '') {
        parent::__construct($id);
        $this->isOneTag = true;
        $this->tag = 'img';
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
}
