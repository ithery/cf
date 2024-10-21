<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Event_OnRenderableAdded {
    /**
     * @var string
     */
    public $renderableClass;

    /**
     * @var content
     */
    public $content;

    /**
     * Create a new event instance.
     *
     * @param string|CRenderable $renderable
     *
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
     * @return CRenderable
     */
    public function getRenderableClass() {
        return $this->renderableClass;
    }

    public function getContent() {
        return $this->content;
    }
}
