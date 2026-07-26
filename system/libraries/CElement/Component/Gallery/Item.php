<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_Gallery_Item extends CElement_Component {
    /**
     * @var string
     */
    protected $type;

    /**
     * @var null|string
     */
    protected $src;

    /**
     * @var null|string
     */
    protected $thumbnail;

    /**
     * @var null|callable|CFunction_SerializableClosure
     */
    protected $imageCallback;

    /**
     * @var CElement_Element_A
     */
    protected $link;

    /**
     * @param string $id
     * @param string $tag
     *
     * @return void
     */
    public function __construct($id = '', $tag = 'div') {
        parent::__construct($id, $tag);
        $this->tag = 'div';
        $this->type = 'image';
        $this->link = $this->addA();
    }

    /**
     * @param string $src
     *
     * @return CElement_Component_Gallery_Item
     */
    public function setSrc($src) {
        $this->src = $src;

        return $this;
    }

    /**
     * @param string $thumbnail
     *
     * @return CElement_Component_Gallery_Item
     */
    public function setThumbnail($thumbnail) {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return void
     */
    private function buildImage() {
        $this->link->addClass('cres-gallery-item')->setAttr('href', $this->src);
        $img = $this->link->addImg()->setSrc($this->thumbnail ?: $this->src);
        if ($this->imageCallback != null) {
            c::call($this->imageCallback, [$img]);
        }
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function withImageCallback($callback) {
        $this->imageCallback = c::toSerializableClosure($callback);

        return $this;
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();
        $this->addClass('cres-gallery-item-wrapper');
        if ($this->type == 'image') {
            $this->buildImage();
        }
    }
}
