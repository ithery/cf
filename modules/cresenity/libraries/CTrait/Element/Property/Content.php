<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:11:13 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Content {

    /**
     *
     * @var string
     */
    protected $content;

    /**
     * Set content of element
     * 
     * @param string $content
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * get content of element 
     * 
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

}
