<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Jul 30, 2020 
 * @license Ittron Global Teknologi
 */
class CManager_Asset_Compiler_Compiled {
    
    protected $filepath;
    
    /**
     * The last modified time of the newest Asset in the Assets array
     * @var int
     */
    protected $lastModTimeNewestAsset = 0;
    
    public function __construct($filepath) {
        $this->filepath = $filepath;
        $this->setLastModTimeOfNewestAsset();
    }
    
    
    
    
    
}
