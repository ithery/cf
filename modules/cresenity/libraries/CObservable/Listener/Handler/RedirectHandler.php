<?php

class CObservable_Listener_Handler_RedirectHandler extends CObservable_Listener_Handler {
    use CTrait_Element_Property_Url;

    public function js() {
        $js = 'window.location.href = ' . json_encode($this->url) . ';';

        return $js;
    }
}
