<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Jul 30, 2020 
 * @license Ittron Global Teknologi
 */
class CManager_Asset_Compiler_BuilderAbstract {

    /**
     *
     * @var array
     */
    protected $files;

    public function __construct($files) {
        $this->files = $files;
    }

}
