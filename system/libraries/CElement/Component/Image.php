<?php

class CElement_Component_Image extends CElement_Component {
    /**
     * @var CElement_Element_Img
     */
    protected $img;

    /**
     * @var string
     */
    protected $progressiveThumbnail;

    public function __construct($id) {
        parent::__construct($id);
        $this->img = $this->addImg();
        $this->wrapper = $this->img;
    }

    public function setProgressiveThumbnail($progressiveThumbnail) {
        $this->progressiveThumbnail = $progressiveThumbnail;

        return $this;
    }

    /**
     * Set Attribute src.
     *
     * @param string $src
     */
    public function setSrc($src) {
        $this->img->setAttr('src', $src);

        return $this;
    }

    /**
     * Set Attribute alt.
     *
     * @param string $alt
     */
    public function setAlt($alt) {
        $this->img->setAttr('alt', $alt);

        return $this;
    }

    public function build() {
        $this->addClass('cres:element:component:Image');
        $this->setAttr('cres-element', 'component:Image');
        if ($this->progressiveThumbnail != null && is_string($this->progressiveThumbnail)) {
            $this->tag = 'a';
            $this->addClass('cres-progressive replace');
            $this->setAttr('href', $this->img->getAttr('src'));
            $this->img->addClass('preview');
            $this->img->setAttr('src', $this->progressiveThumbnail);
        }
    }
}
