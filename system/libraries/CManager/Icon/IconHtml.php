<?php
class CManager_Icon_IconHtml implements CInterface_Htmlable {
    /**
     * @var null|string
     */
    protected $content;

    /**
     * Icon constructor.
     *
     * @param null|string $content
     */
    public function __construct($content = null) {
        $this->content = $content;
    }

    /**
     * @return null|string
     */
    public function toHtml() {
        return $this->content;
    }
}
