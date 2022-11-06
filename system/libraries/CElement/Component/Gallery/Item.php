<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 20, 2018, 2:23:56 AM
 */
class CElement_Component_Gallery_Item extends CElement_Component {
    protected $type;

    protected $src;

    protected $thumbnail;

    protected $imageCallback;

    public function __construct($id = '', $tag = 'div') {
        parent::__construct($id, $tag);
        $this->tag = 'a';
        $this->type = 'image';
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
        $this->setAttr('href', $this->src);
        $img = $this->addImg()->setSrc($this->thumbnail ?: $this->src);
        if ($this->imageCallback != null) {
            c::call($this->imageCallback, [$img]);
        }
    }

    public function withImageCallback($callback) {
        $this->imageCallback = c::toSerializableClosure($callback);

        return $this;
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();
        $this->addClass('cres-gallery-item');
        if ($this->type == 'image') {
            $this->buildImage();
        }
    }
}
