<?php

class CHandlerElement extends CElement {

    public static function factory($id = "", $tag = "div") {
        return new CHandlerElement($id);
    }

}
