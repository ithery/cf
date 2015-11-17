<?php

class CWebsocketClient2 {
    protected $host;
    protected $port;
    protected $target;
    public static function factory($host = 'localhost', $port = '8080') {
        return new CWebsocketClient2($host, $port);
    }
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }
    public function set_target($target) {
        $this->target = $target;
        return $this;
    }
    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append("
        $( document ).ready(function() {
        var last_error = '';
        function generateInterval (k) {
          var maxInterval = (Math.pow(2, k) - 1) * 1000;
          
          if (maxInterval > 30*1000) {
            maxInterval = 30*1000; 
          }
          
          return Math.random() * maxInterval; 
        }
        function chat() {
            var attempts = 1;
            var wsUri = 'ws://" . $this->host . ":" . $this->port . "';    
            websocket = new WebSocket(wsUri); 
            websocket.reconnectInterval = 6000;
            websocket.onopen = function(ev) {
                $('#" . $this->target . "').append('<p>' + 'Connected' + '</p>');
                last_error = '';
            }
            websocket.onmessage = function(ev) {
                var msg = JSON.parse(ev.data);
                var umsg = msg.message;
                var type = msg.type;
                var uname = msg.name;
                var ucolor = msg.color;
                if(type == 'system') {
                    $('#" . $this->target . "').append('<p>' + 'SYSTEM - ' + umsg +'</p>');
                } else {
                    $('#" . $this->target . "').append('<p>' + uname + ' - ' + umsg +'</p>');
                }
            }; 

            websocket.onerror = function(ev){
                if(typeof ev.data != 'undefined') {
                    $('#" . $this->target . "').append('<p>' + 'Error Occurred - ' + ev.data + '</p>');
                }
            }; 

            websocket.onclose = function(ev){
                if(last_error != 'Connection Closed') {
                    last_error = 'Connection Closed';
                    $('#" . $this->target . "').append('<p>' + 'Connection Closed' + '</p>');
                }
                var time = generateInterval(attempts);
    
                setTimeout(function () {
                    attempts++;
                    
                    chat(); 
                }, time);
            };
        };
        chat();
        });");
        return $js->text();
    }
}