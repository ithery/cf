<?php

namespace Cresenity\Documentation;

class Documentation {
    
    protected $path;
    
    /**
     *
     * @var Documentation 
     */
    private static $instance;
    
    /**
     * 
     * @param string $path
     * @return Documentation
     */
    public static function instance($path) {
        if(static::$instance==null) {
            static::$instance = [];
        }
        if(!isset(static::$instance[$path])) {
            static::$instance[$path]=new static($path);
        }
        return static::$instance[$path];
        
    }
    
    private function __construct($path) {
        $this->path = $path;
    }
    
    
    
    public function categories() {
        return [
            [
                'text'=>'Proloque',
                'file'=>'proloque.md',
            ],
            [
                'text'=>'Getting Started',
                'children'=>[
                    [
                        'text'=>'Installation',
                        'file'=>'getting-started/installation.md'
                    ],
                    [
                        'text'=>'Configuration',
                        'file'=>'getting-started/configuration.md'
                    ]
                ],
            ],
        ];
    }
    
    public function categoriesJsTreeData() {
        
    }
}