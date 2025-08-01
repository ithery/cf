<?php

class CApp_React {
    public static function render($componentName, $props, $content = null) {
        $renderer = new CApp_React_Renderer($componentName, $content);

        return $renderer->render($props);
    }
}
