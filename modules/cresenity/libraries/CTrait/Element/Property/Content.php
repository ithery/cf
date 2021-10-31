<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 5:11:13 AM
 */
trait CTrait_Element_Property_Content {
    /**
     * @var string
     */
    protected $content;

    /**
     * Set content of element
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
     * Get content of element
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
}
