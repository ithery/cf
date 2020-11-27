<?php

/**
 * Description of FileEngine
 *
 * @author Hery
 */

namespace Illuminate\View\Engines;

class CView_Engine_FileEngine implements CView_EngineAbstract {

    /**
     * Create a new file engine instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct() {
        
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    public function get($path, array $data = []) {
        return $this->files->get($path);
    }

}
