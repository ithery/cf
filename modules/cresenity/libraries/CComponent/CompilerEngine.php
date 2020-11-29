<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CComponent_CompilerEngine extends CView_Engine_CompilerEngine {

    // Errors thrown while a view is rendering are caught by the Blade
    // compiler and wrapped in an "ErrorException". This makes Livewire errors
    // harder to read, AND causes issues like `abort(404)` not actually working.
    protected function handleViewException( $e, $obLevel) {
        $uses = array_flip(c::classUsesRecursive($e));

        if (
        // Don't wrap "abort(403)".
                $e instanceof AuthorizationException
                // Don't wrap "abort(404)".
                || $e instanceof NotFoundHttpException
                // Don't wrap "abort(500)".
                || $e instanceof HttpException
                // Don't wrap most Livewire exceptions.
                || isset($uses[CComponent_Trait_BypassViewHandlerTrait::class])
        ) {
            // This is because there is no "parent::parent::".
            CView_Engine_PhpEngine::handleViewException($e, $obLevel);

            return;
        }

        parent::handleViewException($e, $obLevel);
    }

}
