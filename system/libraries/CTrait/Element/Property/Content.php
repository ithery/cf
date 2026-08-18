<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CTrait_Element_Property_Content {
    /**
     * @var string
     */
    protected $content;

    /**
     * Set content of element.
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content of element.
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
}
