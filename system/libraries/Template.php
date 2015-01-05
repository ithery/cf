<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Dec 21, 2014
     * @license http://piposystem.com Piposystem
     */

    class Template extends CController {
        
        /**
         *
         * @var CApp 
         */
        protected $_app;
        protected $_template;

        public function __construct() {
            parent::__construct();

            $this->_app = CApp::instance();
        }
        
        protected function render(){
            foreach ($this->_template as $k => $v) {
                $this->_app->set_template($k, $v);
            }
            echo $this->_app->render_template();
        }
    }