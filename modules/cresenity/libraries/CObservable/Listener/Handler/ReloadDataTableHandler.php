<?php

class CObservable_Listener_Handler_ReloadDataTableHandler extends CObservable_Listener_Handler {
    use CObservable_Listener_Handler_Trait_TargetHandlerTrait,
        CObservable_Listener_Handler_Trait_SelectorHandlerTrait;

    public function __construct($listener) {
        parent::__construct($listener);

        $this->target = '';
        $this->selector = '';
    }

    public function js() {
        $selector = $this->getSelector();

        $js = "$('" . $selector . "').DataTable().ajax.reload()";

        return $js;
    }
}
