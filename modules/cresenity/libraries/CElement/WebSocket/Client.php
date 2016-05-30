<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  May 23, 2016
     * @license http://ittron.co.id ITtron
     */
    class CElement_WebSocket_Client extends CElement {

        protected $host;
        protected $port;
        protected $server;
        protected $auth_id;
        protected $func_name;
        protected $reconnect_interval;
        protected $on_open;
        protected $on_message;
        protected $on_error;
        protected $on_close;

        public function __construct($id = "", $tag = "div") {
            parent::__construct($id, $tag);

            $this->host = 'localhost';
            $this->port = '8080';
            $this->server = 'socket/server';
            $this->func_name = 'client';
            if (strlen($this->id) > 0) {
                if (!is_numeric($this->id)) {
                    $this->func_name = str_replace('-', '_', $this->id);
                }
            }
            $this->reconnect_interval = 1;
            $this->on_open = null;
            $this->on_message = null;
            $this->on_error = null;
            $this->on_close = null;
        }

        public static function factory($id = "", $tag = "div") {
            return new Client($id, $tag);
        }

        public function html($indent = 0) {
            $html = CStringBuilder::factory();

            $html->append(parent::html($indent));
            return $html->text();
        }

        public function js($indent = 0) {
            $js = CStringBuilder::factory();

            $this->func_name = 'webSocket_' . $this->func_name;
            $data_login = array(
                'act' => 'login',
                'auth_id' => $this->auth_id
            );

            $js->appendln('
                var last_error' . $this->func_name . ' = "";
                function generateInterval (k) {
                    var maxInterval = (Math.pow(2, k) - 1) * 1000;
                    if (maxInterval > 30*1000) {
                        maxInterval = 30*1000; 
                    }
                    return Math.random() * maxInterval; 
                }'
            );

            $js->appendln('
                function ' . $this->func_name . '(){
                    try {
                        var attempts = 1;
                        var wsUri = "ws://' . $this->host . ':' . $this->port . '/' . $this->server . '";
                        websocket = new WebSocket(wsUri);
                        websocket.reconnectInterval = ' . $this->reconnect_interval . ';'
            );
            if (strlen($this->on_open) > 0) {
                $js->appendln('
                        websocket.onopen = function(ev){
                            websocket.send(JSON.stringify(' . json_encode($data_login) . '));
                                last_error' . $this->func_name . ' = "";
                        ' . $this->on_open . '
                        };'
                );
            }
            if (strlen($this->on_message) > 0) {
                $js->appendln('
                        websocket.onmessage = function(ev){
                        ' . $this->on_message . '
                        };'
                );
            }
            if (strlen($this->on_error) > 0) {
                $js->appendln('
                        websocket.onerror = function(ev){
                            $.cresenity.message("error","Socket error connection.");
                        ' . $this->on_error . '
                        };'
                );
            }
            if (strlen($this->on_close) > 0) {
                $js->appendln('
                        websocket.onclose = function(ev){
                            ' . $this->on_close . '
                            var time = generateInterval(attempts);
    
                            setTimeout(function () {
                                attempts++;
                                chat(); 
                            }, time);
                        };'
                );
            }
            $js->append('
                    }
                    catch (e) {
                        $.cresenity.message("error","Socket error connection.");
                    }
                }');
            $js->append($this->func_name . '();');

            $js->append(parent::js($indent));
            return $js->text();
        }

        function get_host() {
            return $this->host;
        }

        function get_port() {
            return $this->port;
        }

        function set_host($host) {
            $this->host = $host;
            return $this;
        }

        function set_port($port) {
            $this->port = $port;
            return $this;
        }

        function get_auth_id() {
            return $this->auth_id;
        }

        function set_auth_id($auth_id) {
            $this->auth_id = $auth_id;
            return $this;
        }

    }
    