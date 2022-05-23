<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Dec 7, 2020
 */
trait CApp_Concern_ViewElementTrait {
    private static $renderingElement;

    public static function renderingElement() {
        return static::$renderingElement;
    }

    public static function setRenderingElement(&$element) {
        static::$renderingElement = $element;
    }

    /**
     * Get the string contents of a push section.
     *
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public function yieldViewElement($key, $default = '') {
        $element = null;
        $output = '';
        if ($key instanceof Closure) {
            $element = c::value($key);
        } else {
            $renderingElement = self::renderingElement();
            if ($renderingElement == null) {
                return $default;
            }

            if ($renderingElement instanceof CElement_View) {
                $element = $renderingElement->viewElement($key);
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

    /**
     * Flush all of the stacks.
     *
     * @return void
     */
    public function flushViewElements() {
        $this->viewElements = [];
    }
}
