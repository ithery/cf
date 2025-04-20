<?php

use Illuminate\Contracts\Support\Renderable;

/**
 * @deprecated 1.8 use Illuminate\Contracts\Support\Renderable
 */
interface CInterface_Renderable extends Renderable {
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();
}
