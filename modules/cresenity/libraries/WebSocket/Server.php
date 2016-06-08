<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  May 23, 2016
     */
    class WebSocket_Server extends WebSocket_SocketEngine {

        protected $host;
        protected $port;
        protected $clients;
        protected $clients_data;
        protected $valid_clients;
        protected $send_all_when_connect;

        public function __construct($host, $port) {
            parent::__construct($host, $port);
            $this->clients = array();
            $this->clients_data = array();
            $this->valid_clients = array();
        }

        public static function factory($host = 'localhost', $port = '8080') {
            return new WebSocket_Server($host, $port);
        }

        public function exec() {
            $port = $this->port;
            $host = $this->host;

            $null = NULL; //null var
            //reuseable port
            socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);

            //bind socket to specified host
            socket_bind($this->socket, 0, $port);

            //listen to port
            socket_listen($this->socket);

            //create & add listning socket to the list
            $this->clients = array($this->socket);

            //start endless loop, so that our script doesn't stop
            while (true) {
                //manage multipal connections
                $changed = $this->clients;
                //returns the socket resources in $changed array
                socket_select($changed, $null, $null, 0, 10);

                //check for new socket
                if (in_array($this->socket, $changed)) {
                    $socket_new = socket_accept($this->socket); //accpet new socket
                    $this->clients[] = $socket_new; //add socket to client array

                    $header = socket_read($socket_new, 1024); //read data sent by the socket
                    $this->perform_handshaking($header, $socket_new, $host, $port); //perform websocket handshake

                    try {
                        @socket_getpeername($socket_new, $ip); //get ip address of connected socket
                        $data_response = array(
                            'act' => 'info',
                            'type' => 'system',
                            'data' => array(
                                'status' => 'connected',
                                'message' => $ip . ' connected'
                            )
                        );

                        $client_notify = array($socket_new);
                        if ($this->send_all_when_connect) {
                            $client_notify = $this->clients;
                        }
                        $response = $this->mask(json_encode($data_response)); //prepare json data
                        $this->send_message($client_notify, $response); //notify all users about new connection
                        //make room for new socket
                        $found_socket = array_search($this->socket, $changed);
                        unset($changed[$found_socket]);
                    }
                    catch (Exception $exc) {
                        // do nothing
                        // this catch for handle 
                        //   - socket_getpeername(): unable to retrieve peer name [107]: Transport endpoint is not connected
                    }
                }

                //loop through all connected sockets
                foreach ($changed as $changed_index => $changed_socket) {

                    //check for any incomming data
                    while (@socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
                        $received_text = $this->unmask($buf); //unmask data
                        $message = json_decode($received_text, true); //json decode 

                        $message['ipaddress'] = $ip;
                        $this->receive_message($changed_index, $message, $clients_receiver); // do something receive message
                        //prepare data to be sent to client
                        $response_text = $this->mask(json_encode($message));
                        $this->send_message($clients_receiver, $response_text); //send data
                        break 2; //exist this loop
                    }

                    $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
                    if ($buf === false) { // check disconnected client
                        // remove client for $clients array
                        $found_socket = array_search($changed_socket, $this->clients);
                        try {
                            @socket_getpeername($changed_socket, $ip);
                            $data_response = array(
                                'act' => 'info',
                                'type' => 'system',
                                'data' => array(
                                    'status' => 'disconnected',
                                    'message' => $ip . ' disconnected'
                                )
                            );
                            unset($this->clients[$found_socket]);
                            if (isset($this->clients_data[$found_socket])) {
                                $app_id = carr::get($this->clients_data[$found_socket], 'app_id');
                                $valid_clients = carr::get($this->valid_clients, $app_id, array());
                                foreach ($valid_clients as $k => $v) {
                                    if (carr::get($v, 'client_idx') == $found_socket) {
                                        unset($this->valid_clients[$app_id][$k]);
                                        break;
                                    }
                                }
                                unset($this->clients_data[$found_socket]);
                            }

                            //notify all users about disconnected connection
                            $response = $this->mask(json_encode($data_response));
                            if ($this->send_all_when_connect) {
                                $this->send_message($this->clients, $response);
                            }
                        }
                        catch (Exception $ex) {
                            // do nothing
                            // this catch for handle 
                            //   - socket_getpeername(): unable to retrieve peer name [107]: Transport endpoint is not connected
                        }
                    }
                }
            }
        }

        protected function send_message($clients, $msg) {
            foreach ($clients as $changed_socket) {
                @socket_write($changed_socket, $msg, strlen($msg));
            }
            return true;
        }

        // overwrite this message to do anything when receive a message
        protected function receive_message($changed_index, &$message, &$clients_receiver) {
            return;
        }

        //Unmask incoming framed message
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

        //handshake new client.
        protected function perform_handshaking($receved_header, $client_conn, $host, $port) {
            $headers = array();
            $lines = preg_split("/\r\n/", $receved_header);
            foreach ($lines as $line) {
                $line = chop($line);
                if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                    $headers[$matches[1]] = $matches[2];
                }
            }
            $err_code = 0;
//            $secKey = $headers['Sec-WebSocket-Key'];
            $secKey = carr::get($headers, 'Sec-WebSocket-Key');
            if (strlen($secKey) == 0) {
                $err_code++;
            }
            if ($err_code == 0) {
                $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
                //hand shaking header
                $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
                        "Upgrade: websocket\r\n" .
                        "Connection: Upgrade\r\n" .
                        "WebSocket-Origin: $host\r\n" .
                        "WebSocket-Location: ws://$host:$port/socket/server\r\n" .
                        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
            }
            else {
                // later block if there is wrong format request
                $upgrade = "HTTP/1.1 400 Bad Request\r\n" .
                        "Upgrade: websocket\r\n" .
                        "Connection: Closed\r\n" .
                        "WebSocket-Origin: $host\r\n" .
                        "WebSocket-Location: ws://$host:$port/socket/server\r\n\r\n";
            }
            // need log later if socket_write is failed
            @socket_write($client_conn, $upgrade, strlen($upgrade));
        }

        function get_send_all_when_connect() {
            return $this->send_all_when_connect;
        }

        function set_send_all_when_connect($send_all_when_connect) {
            $this->send_all_when_connect = $send_all_when_connect;
            return $this;
        }

    }
    