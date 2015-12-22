<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Aug 4, 2015
     * @license http://piposystem.com Piposystem
     */
    class Api {
        
        const __GET = 'GET';
        const __POST = 'POST';
        const __GET_POST = 'GET_POST';

        protected $service;
        protected $request;
        protected $response;
        protected $method;
        protected $class;
        protected $debug = false;
        protected $generate_doc = false;
        
        protected function __construct(){
            $this->service = array();
        }
        
        public function set_method($method) {
            switch ($method) {
                // this case is for validation method, so just list available method
                case Api::__GET:
                case Api::__POST:   
                case Api::__GET_POST:   
                    break;
                default:
                    die($method ." doesn't support.");
                    break;
            }
            $this->method = $method;
            return $this;
        }
        
        public function get_method(){
            return $this->method;
        }
        
        protected function xml2array($xml) {
            $arr = array();

            foreach ($xml->children() as $r) {
                $t = array();
                if (count($r->children()) == 0) {
                    $arr[$r->getName()] = strval($r);
                }
                else {
                    $arr[$r->getName()][] = $this->xml2array($r);
                }
            }
            return $arr;
        }
        
        final public function get_request() {
            return $this->request;
        }
        
        final public function get_response() {
            return $this->response;
        }
        
        final public function get_service() {
            return $this->service;
        }
        
        final public function get_generate_doc() {
            return $this->generate_doc;
        }

        final public function set_generate_doc($generate_doc) {
            $this->generate_doc = $generate_doc;
        }

    
    }
    