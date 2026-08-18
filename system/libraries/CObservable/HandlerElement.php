<?php

class CObservable_HandlerElement extends CElement {
    public static function factory($id = '', $tag = 'div') {
        return new CObservable_HandlerElement($id, $tag);
    }
}
