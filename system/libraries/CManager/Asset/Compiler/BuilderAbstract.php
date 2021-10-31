<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Jul 30, 2020
 */
class CManager_Asset_Compiler_BuilderAbstract {
    /**
     * @var array
     */
    protected $files;

    public function __construct($files) {
        $this->files = $files;
    }
}
