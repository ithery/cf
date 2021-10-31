<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CComponent_CompilerEngine extends CView_Engine_CompilerEngine {
    /**
     * Errors thrown while a view is rendering are caught by the Blade
     * compiler and wrapped in an "ErrorException". This makes Component errors
     * harder to read, AND causes issues like `abort(404)` not actually working.
     *
     * @param \Throwable $e
     * @param int        $obLevel
     *
     * @return \Throwable
     */
    protected function handleViewException($e, $obLevel) {
        $uses = array_flip(c::classUsesRecursive($e));
        // Don't wrap "abort(403)".
        // Don't wrap "abort(404)".
        // Don't wrap "abort(500)".
        // Don't wrap most Livewire exceptions.
        if ($e instanceof AuthorizationException
            || $e instanceof NotFoundHttpException
            || $e instanceof HttpException
            || isset($uses[CComponent_Exception_BypassViewHandlerTrait::class])
        ) {
            // This is because there is no "parent::parent::".
            CView_Engine_PhpEngine::handleViewException($e, $obLevel);

            return;
        }

        parent::handleViewException($e, $obLevel);
    }
}
