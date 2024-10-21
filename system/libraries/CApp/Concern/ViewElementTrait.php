<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CApp_Concern_ViewElementTrait {
    /**
     * Get the string contents of a push section.
     *
     * @param string $key
     * @param string $default
     * @param mixed  $element
     *
     * @return string
     */
    public function yieldViewElement($element, $key, $default = '') {
        $output = '';
        if ($key instanceof Closure) {
            $element = c::value($key);
        } else {
            if ($element == null) {
                return $default;
            }

            if ($element instanceof CElement_View) {
                $element = $element->viewElement($key);
            }
        }
        if ($element != null && $element instanceof CRenderable) {
            $output .= $element->html();
            $js = $element->js();
            if (strlen($js) > 0) {
                c::app()->addJs($js);
            }
        }

        return $output;
    }
}
