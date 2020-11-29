<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_ComponentCompilerEngine extends CComponent_CompilerEngine {

    protected $component;
    protected $isRenderingComponent = false;

    public function startComponentRendering($component) {
        $this->component = $component;
        $this->isRenderingComponent = true;
    }

    public function endComponentRendering() {
        $this->isRenderingComponent = false;
    }

    public function setComponent($component) {
        $this->component = $component;
    }

    protected function evaluatePath($__path, $__data) {
        if (!$this->isRenderingComponent) {
            return parent::evaluatePath($__path, $__data);
        }

        $obLevel = ob_get_level();

        ob_start();

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            \Closure::bind(function() use($__path, $__data) {
                extract($__data, EXTR_SKIP);
                include $__path;
            }, $this->component ? $this->component : $this)();
        } catch (Exception $e) {
            $this->handleViewException($e, $obLevel);
        } catch (Throwable $e) {
            $this->handleViewException($e, $obLevel);
        }

        return ltrim(ob_get_clean());
    }

}
