<?php

require_once dirname(__FILE__).DS.'CWebsocketClientAbstractSocketIOEngine.php';
require_once dirname(__FILE__).DS.'CWebsocketClientAbstractPayload.php';
require_once dirname(__FILE__).DS.'CWebsocketClientDecoder.php';
require_once dirname(__FILE__).DS.'CWebsocketClientEncoder.php';
require_once dirname(__FILE__).DS.'CWebsocketClientSession.php';
class CWebsocketClientSocketIOEngine extends CWebsocketClientAbstractSocketIOEngine {

	
    const CLOSE         = 0;
    const OPEN          = 1;
    const HEARTBEAT     = 2;
    const MESSAGE       = 3;
    const JOIN_MESSAGE  = 4;
    const EVENT         = 5;
    const ACK           = 6;
    const ERROR         = 7;
    const NOOP          = 8;
    const TRANSPORT_POLLING   = 'xhr-polling';
    const TRANSPORT_WEBSOCKET = 'websocket';
    /** {@inheritDoc} */
    public function connect()
    {
        if (is_resource($this->stream)) {
            return;
        }
        $this->handshake();
        $errors = array(null, null);
        $host   = sprintf('%s:%d', $this->url['host'], $this->url['port']);
        if (true === $this->url['secured']) {
            $host = 'ssl://' . $host;
        }
        $this->stream = stream_socket_client($host, $errors[0], $errors[1], $this->options['timeout'], STREAM_CLIENT_CONNECT, stream_context_create($this->options['context']));
        if (!is_resource($this->stream)) {
            throw new SocketException($error[0], $error[1]);
        }
        stream_set_timeout($this->stream, $this->options['timeout']);
        $this->upgrade_transport();
    }
    /** {@inheritDoc} */
    public function close()
    {
        if (!is_resource($this->stream)) {
            return;
        }
        fclose($this->stream);
        $this->stream = null;
        $this->session = null;
    }
    /** {@inheritDoc} */
    public function emit($event, array $args)
    {
		
        $this->write(self::EVENT, json_encode(array('name' => $event, 'args' => $args)));
    }
    /** {@inheritDoc} */
    public function write($code, $message = null)
    {
        if (!is_resource($this->stream)) {
            return;
        }
        if (!is_int($code) || 0 > $code || 6 < $code) {
            throw new InvalidArgumentException('Wrong message type when trying to write on the socket');
        }
		
        $payload = new CWebsocketClientEncoder($code . ':::' . $message, CWebsocketClientEncoder::OPCODE_TEXT, true);

        $bytes = fwrite($this->stream, (string) $payload);
		
		
		$response = fgets($this->stream,200);
		
        // wait a little bit of time after this message was sent
        usleep($this->options['wait']);
        return $bytes;
    }
    /** {@inheritDoc} */
    public function get_name()
    {
        return 'SocketIO Version 0.X';
    }
    /** {@inheritDoc} */
    protected function get_default_options()
    {
        $defaults = parent::get_default_options();
        $defaults['protocol']  = 1;
        $defaults['transport'] = self::TRANSPORT_WEBSOCKET;
        return $defaults;
    }
    /** Does the handshake with the Socket.io server and populates the `session` value object */
    protected function handshake()
    {
        if (null !== $this->session) {
            return;
        }
        $url = sprintf('%s://%s:%d/%s/%d', $this->url['scheme'], $this->url['host'], $this->url['port'], trim($this->url['path'], '/'), $this->options['protocol']);
        if (isset($this->url['query'])) {
            $url .= '/?' . http_build_query($this->url['query']);
        }
		
		
        $result = @file_get_contents($url, false, stream_context_create(array('http' => array('timeout' => (float) $this->options['timeout']))));
        if (false === $result) {
            throw new Exception();
        }
		
        $sess = explode(':', $result);
        $decoded['sid'] = $sess[0];
        $decoded['pingInterval'] = $sess[1];
        $decoded['pingTimeout'] = $sess[2];
        $decoded['upgrades'] = array_flip(explode(',', $sess[3]));
		
        if (!in_array('websocket', $decoded['upgrades'])) {
            throw new UnsupportedTransportException('websocket');
        }
        $this->session = new CWebsocketClientSession($decoded['sid'], $decoded['pingInterval'], $decoded['pingTimeout'], $decoded['upgrades']);
    }
    /** Upgrades the transport to WebSocket */
    private function upgrade_transport()
    {
		
        if (!array_key_exists('websocket', $this->session->upgrades)) {
            throw new Exception('websocket');
        }
		
        $url = sprintf('/%s/%d/%s/%s', trim($this->url['path'], '/'), $this->options['protocol'], $this->options['transport'], $this->session->id);
        if (isset($this->url['query'])) {
            $url .= '/?' . http_build_query($this->url['query']);
        }
		
		//$url = '/socket.io/1/websocket/X6rc4QXGbyQWJT1XDxIU';
		
        $key = base64_encode(sha1(uniqid(mt_rand(), true), true));
        $request = "GET {$url} HTTP/1.1\r\n"
                 . "Host: {$this->url['host']}\r\n"
                 . "Upgrade: WebSocket\r\n"
                 . "Cache-Control: no-cache\r\n"
                 . "Sec-WebSocket-Key: {$key}\r\n"
                 . "Sec-WebSocket-Version: 13\r\n"
                 . "Origin: *\r\n\r\n";
        
		




		fwrite($this->stream, $request);
        $result = fread($this->stream, 128);
		$result12 = substr($result,0,12);
		
        if ('HTTP/1.1 101' !== $result12) {
            throw new Exception(sprintf('The server returned an unexpected value. Expected "HTTP/1.1 101", had "%s"', $result));
        }
		// preg_match('#Sec-WebSocket-Accept:\s(.*)$#mU', $result, $matches);
		// if ($matches) {
			// $keyAccept = trim($matches[1]);
			// $expectedResonse = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
			
			// if($keyAccept !== $expectedResonse) {
				// throw new Exception(sprintf('Unexpected response for key accept "%s"', $keyAccept));
			// }
		// }
		
        // cleaning up the stream
        //while ('' !== trim(fgets($this->stream)));
    }
}
