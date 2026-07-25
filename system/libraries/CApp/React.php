<?php

class CApp_React {
    /**
     * Render a react component to HTML.
     *
     * @param string      $componentName
     * @param array       $props
     * @param null|string $content
     *
     * @return string
     */
    public static function render($componentName, $props, $content = null) {
        $renderer = new CApp_React_Renderer($componentName, $content);

        return $renderer->render($props);
    }
}
