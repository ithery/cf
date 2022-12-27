<?php

class CApp_React {
    public static function render($componentName, $props) {
        $renderer = new CApp_React_Renderer($componentName);

        return $renderer->render($props);
    }
}
