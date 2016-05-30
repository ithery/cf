<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  May 23, 2016
     */
    class WebSocket_Client extends WebSocket_SocketEngine {

        protected $origin;
        protected $url_server;
        protected $user_agent;
        protected $auth_id;
        
        // initialize for encoding
        protected $rsv = array(0, 0, 0);
        protected $fin = 1;
        protected $op_code = 0x1;
        protected $mask = false;
        protected $mask_key;
        
        public function __construct($host, $port) {
            
//            $host = 'socket.local';
            $host = 'ittronaero.com';
            $port = '8082';
            parent::__construct($host, $port);
            
            $this->url_server = '/socket/server';
            $this->origin = 'http://intern.local';
//            $this->auth_id = 'f465893674d0f121bddb96698d6c55b5';
            $this->user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36";
        }

        public static function factory($host = 'localhost', $port = '8080') {
            return new WebSocket_Client($host, $port);
        }

        protected function perform_handshacking() {
            $this->sec_websocket_key = base64_encode($this->generateRandomWebSocketKey());
            $message = "GET " . $this->url_server . " HTTP/1.1\r\n"
                    . "Host: " . $this->host . "\r\n"
                    . "Connection: Upgrade\r\n"
                    . "Pragma: no-cache\r\n"
                    . "Cache-Control: no-cache\r\n"
                    . "Upgrade: websocket\r\n"
                    . "Origin: " . $this->origin . "\r\n"
                    . "Sec-WebSocket-Version: 13\r\n"
                    . "User-Agent: " . $this->user_agent . "\r\n"
                    . "Accept-Encoding: gzip, deflate, sdch\r\n"
                    . "Accept-Language: id-ID,id;q=0.8,en-US;q=0.6,en;q=0.4\r\n"
                    . "Sec-WebSocket-Key: " . $this->sec_websocket_key . "\r\n"
                    . "Sec-WebSocket-Extensions: permessage-deflate; client_max_window_bits\r\n\r\n";
            //Send the message to the server
            if (!@socket_send($this->socket, $message, strlen($message), 0)) {
                throw new Exception('[HANDSHACKE] ' .$this->last_error());
            }
        }

        protected function generateRandomWebSocketKey() {
            return sha1(uniqid(mt_rand(), true), true);
        }

        protected $connect_response;
        function get_connect_response() {
            return $this->connect_response;
        }

        function set_connect_response($connect_response) {
            $this->connect_response = $connect_response;
            return $this;
        }

        public function connect() {
            // Connect socket to remote server
            if (!@socket_connect($this->socket, $this->host, $this->port)) {
                throw new Exception('[CONN] Host: ' .$this->host .', Port: ' .$this->port .'.' . $this->last_error());
            }

            // handshake first with server
            $this->perform_handshacking();

            //Now receive reply from server. This response is response from handshake
            if (@socket_recv($this->socket, $buf, 1024, 0) === FALSE) {
                throw new Exception('[RECV-1] ' . $this->last_error());
            }
            $this->connect_response['handshake'] = $this->read_response($buf); //unmask data

            //Now receive reply from server. This response is info if this socket is connected
            if (@socket_recv($this->socket, $buf, 1024, 0) === FALSE) {
                throw new Exception('[RECV-2]. ' . $this->last_error());
            }
            $received_text = $this->read_response($buf); //unmask data
            $this->connect_response['connect'] = $received_text;
            $message = json_decode($received_text, true); //json decode 

            $data = carr::get($message, 'data');
            if (count($data) == 0) {
                throw new Exception(clang::__('No data response from server'));
            }

            $status = carr::get($data, 'status');
            if (strtolower($status) != 'connected') {
                throw new Exception(clang::__('Cant connect to server'));
            }
            return $this;
        }

        public function generateRandomdMT($length) {
            $validCharacters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $myKeeper = '';
            for ($n = 1; $n <= $length; $n++) {
                $whichCharacter = mt_rand(0, strlen($validCharacters) - 1);
                $myKeeper .= $validCharacters{$whichCharacter};
            }
            return $myKeeper;
        }

        protected function encode($text) {
            $pack = '';
            $this->mask_key = $this->generateRandomdMT(4);
            $length = strlen($text);
            if (0xFFFF < $length) {
                $pack = pack('NN', ($length & 0xFFFFFFFF00000000) >> bindec(100000), $length & 0x00000000FFFFFFFF);
                $length = 0x007F;
            }
            elseif (0x007D < $length) {
                $pack = pack('n*', $length);
                $length = 0x007E;
            }
            $payload = ($this->fin << 1) | $this->rsv[0];
            $payload = ($payload << 1) | $this->rsv[1];
            $payload = ($payload << 1) | $this->rsv[2];
            $payload = ($payload << 4) | $this->op_code;
            $payload = ($payload << 1) | $this->mask;
            $payload = ($payload << 7) | $length;

            $data = $text;
            $payload = pack('n', $payload) . $pack;

            $payload .= $this->mask_key;

            $data = $this->mask_data($data);

            $payload = $payload . $data;
            return $payload;
        }

        protected function mask_data($data) {
            $masked = '';
            $data = str_split($data);
            $key = str_split($this->mask_key);

            foreach ($data as $i => $letter) {
                $masked .= $letter ^ $key[$i % 4];
            }
            return $masked;
        }

        public function login($auth_id = null) {
            if (strlen($auth_id) > 0) {
                $this->auth_id = $auth_id;
            }
            $data_login = array(
                'act' => 'login',
                'auth_id' => $this->auth_id
            );
            $text = json_encode($data_login);
            try {
                // send message login to server
                $recv_text = $this->send_message($text);
                $response = json_decode($recv_text, true);

                $act = carr::get($response, 'act');
                $error = carr::get($response, 'error');
                $message = carr::get($response, 'message');
                $data = carr::get($response, 'data');
                if ($error == false) {
                    if ($act == 'login') {
                        return $data;
                    }
                    else {
                        throw new Exception('ERROR[Login-2]. Wrong ACT response. Please contact server administrator');
                    }
                }
                else {
                    throw new Exception('ERROR[Login-1]. ' . $message);
                }
            }
            catch (Exception $exc) {
                throw new Exception('ERROR[Login]. ' . $exc->getMessage());
            }
        }

        public function send_message($msg) {
            // encode message
            $msg = $this->encode($msg);

            if (!@socket_write($this->socket, $msg, strlen($msg))) {
                throw new Exception('[WRITE] ' . $this->last_error() . '. Message: ' . $msg);
            }
            if (@socket_recv($this->socket, $buf, 1024, 0) === FALSE) {
                throw new Exception('[RECV-3] ' . $this->last_error());
            }
            
            $received_text = $this->read_response($buf); //unmask data
            return $received_text;
        }

        public function close() {
            if (!(@socket_close($this->socket))) {
                throw new Exception('[CLOSED] ' . $this->last_error());
            }
            return $this;
        }

        // Read and Decode message from server
        function read_response($text) {
            $length = ord($text[1]) & 127;
            $data = substr($text, $length * -1, $length);
            return $data;
        }

        function get_origin() {
            return $this->origin;
        }

        function get_url_server() {
            return $this->url_server;
        }

        function get_user_agent() {
            return $this->user_agent;
        }

        function get_auth_id() {
            return $this->auth_id;
        }

        function set_origin($origin) {
            $this->origin = $origin;
            return $this;
        }

        function set_url_server($url_server) {
            $this->url_server = $url_server;
            return $this;
        }

        function set_user_agent($user_agent) {
            $this->user_agent = $user_agent;
            return $this;
        }

        function set_auth_id($auth_id) {
            $this->auth_id = $auth_id;
            return $this;
        }

    }
    