<?php

/**
 * Description of Reloadable
 *
 * @author Hery
 */
trait CTrait_Element_Handler_ReloadHandler {
    /**
     * Reload Handler
     *
     * @var CObservable_Listener_Handler_ReloadHandler
     */
    protected $reloadHandler;

    /**
     * @return CObservable_Listener_Handler_ReloadHandler
     */
    public function reloadHandler() {
        if ($this->reloadHandler == null) {
            $listener = $this->addListener('ready');

            $this->reloadHandler = new CObservable_Listener_Handler_ReloadHandler($listener);
            $this->reloadHandler->setSelector('#' . $this->id());
            $listener->addHandler($this->reloadHandler);
        }
        return $this->reloadHandler;
    }

    public function bootBuildReloadHandler() {
        if ($this->reloadHandler) {
            $attributes = $this->reloadHandler->toAttributeArray();
            foreach ($attributes as $key => $value) {
                $this->setAttr('data-' . cstr::snake($key, '-'), c::html($value, ENT_QUOTES));
            }
        }
    }
}
