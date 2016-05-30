<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  May 23, 2016
     */
    class WebSocket_SocketEngine {

        protected $sec_websocket_key;
        protected $socket;
        protected $host;
        protected $port;
        
        public function __construct($host, $port) {
            $this->host = $host;
            $this->port = $port;
            
            //Create TCP/IP sream socket
            if (!($this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
                throw new Exception('[SOCKCREATE]. ' .$this->last_error());
            }
        }
        
        protected function last_error(){
            $error_code = socket_last_error();
            $error_msg = socket_strerror($error_code);
            return "Couldn't create socket: [" . $error_code . "] " . $error_msg . " ";
        }
        
       

        // Unmask incoming framed message
        protected function unmask($text) {
            $length = ord($text[1]) & 127;
            if ($length == 126) {
                $masks = substr($text, 4, 4);
                $data = substr($text, 8);
            }
            elseif ($length == 127) {
                $masks = substr($text, 10, 4);
                $data = substr($text, 14);
            }
            else {
                $masks = substr($text, 2, 4);
                $data = substr($text, 6);
            }
            $text = "";
            for ($i = 0; $i < strlen($data); ++$i) {
                $text .= $data[$i] ^ $masks[$i % 4];
            }
            return $text;
        }

        //Encode message for transfer to client.
        protected function mask($text) {
            $b1 = 0x80 | (0x1 & 0x0f);
            $length = strlen($text);

            if ($length <= 125) $header = pack('CC', $b1, $length);
            elseif ($length > 125 && $length < 65536)
                    $header = pack('CCn', $b1, 126, $length);
            elseif ($length >= 65536) $header = pack('CCNN', $b1, 127, $length);
            return $header . $text;
        }

    }
    