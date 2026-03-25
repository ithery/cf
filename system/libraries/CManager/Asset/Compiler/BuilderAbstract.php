<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_Asset_Compiler_BuilderAbstract {
    /**
     * @var array
     */
    protected $files;

    public function __construct($files) {
        $this->files = $files;
    }
}
