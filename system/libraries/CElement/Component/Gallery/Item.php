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
        $this->addImg()->setSrc($this->thumbnail ?: $this->src);
    }

    /**
     * @return void
     */
    protected function build() {
        $this->addClass('cres-gallery-item');
        if ($this->type == 'image') {
            $this->buildImage();
        }
    }
}
