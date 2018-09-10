<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 1:55:18 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Event_OnRenderableAdded {

    /**
     *
     * @var string
     */
    public $renderableClass;

    /**
     *
     * @var content
     */
    public $content;

    /**
     * Create a new event instance.
     *
     * @param  string|CRenderable  $renderable
     * @return void
     */
    public function __construct($renderable) {
        if ($renderable instanceof CRenderable) {
            $this->renderableClass = get_class($renderable);
        } else {
            $this->content = $renderable;
        }
    }

    /**
     * 
     * @return CRenderable
     */
    public function getRenderableClass() {
        return $this->renderableClass;
    }

    public function getContent() {
        return $this->content;
    }

}
