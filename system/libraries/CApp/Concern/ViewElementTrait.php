<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Dec 7, 2020
 */
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
                // if (!c::app()->isAjax()) {
                //     $js = '<script>' . $js . '</script>';
                // }

                // $this->extendPush('capp-script', $js);
                c::app()->addJs($js);
            }
        }

        return $output;
    }
}
