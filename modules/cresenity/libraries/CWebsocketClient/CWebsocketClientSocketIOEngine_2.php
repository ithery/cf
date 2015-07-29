<?php

require_once dirname(__FILE__).DS.'CWebsocketClientAbstractSocketIOEngine.php';
require_once dirname(__FILE__).DS.'CWebsocketClientAbstractPayload.php';
require_once dirname(__FILE__).DS.'CWebsocketClientDecoder.php';
require_once dirname(__FILE__).DS.'CWebsocketClientEncoder.php';
require_once dirname(__FILE__).DS.'CWebsocketClientSession.php';
class CWebsocketClientSocketIOEngine extends CWebsocketClientAbstractSocketIOEngine {

	
    const TRANSPORT_POLLING   = 'polling';
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
            throw new SocketException($errors[0], $errors[1]);
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
        $this->write(self::CLOSE);
        fclose($this->stream);
        $this->stream = null;
        $this->session = null;
    }
    /** {@inheritDoc} */
    public function emit($event, array $args)
    {
        return $this->write(self::MESSAGE, self::EVENT . json_encode(array($event, $args)));
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
        $payload = new CWebsocketClientEncoder($code . $message, CWebsocketClientEncoder::OPCODE_TEXT, true);
        
		$bytes = fwrite($this->stream, (string) $payload);
        // wait a little bit of time after this message was sent
        usleep((int) $this->options['wait']);
        return $bytes;
    }
    /** {@inheritDoc} */
    public function get_name()
    {
        return 'SocketIO Version 1.X';
    }
    /** {@inheritDoc} */
    protected function get_default_options()
    {
        $defaults = parent::get_default_options();
        $defaults['version']   = 2;
        $defaults['use_b64']   = false;
        $defaults['transport'] = self::TRANSPORT_POLLING;
        return $defaults;
    }
    
	protected function handshake()
    {
        if (null !== $this->session) {
            return;
        }
        $query = array('use_b64'   => $this->options['use_b64'],
                  'EIO'       => $this->options['version'],
                  'transport' => $this->options['transport']);
        if (isset($this->url['query'])) {
            $query = carr::replace($query, $this->url['query']);
        }
        $context = $this->options['context'];
        $context['http'] = array('timeout' => (float) $this->options['timeout']);
        $url    = sprintf('%s://%s:%d/%s/?%s', $this->url['scheme'], $this->url['host'], $this->url['port'], trim($this->url['path'], '/'), http_build_query($query));
        $result = @file_get_contents($url, false, stream_context_create($context));
        if (false === $result) {
            throw new ServerConnectionFailureException;
        }
        $decoded = json_decode(substr($result, strpos($result, '{')), true);
		if(is_array($decoded['upgrades'])) {
		
		
			if (!in_array('websocket', $decoded['upgrades'])) {
				throw new UnsupportedTransportException('websocket');
			}
		}
		$upgrades = $decoded['upgrades'];
		if(!is_array($upgrades)) $upgrades = array($upgrades);
        $this->session = new CWebsocketClientSession($decoded['sid'], $decoded['pingInterval'], $decoded['pingTimeout'], $upgrades);
    }
    /** Upgrades the transport to WebSocket */
    private function upgrade_transport() {
        // $query = array('sid'       => $this->session->id,
                  // 'EIO'       => $this->options['version'],
                  // 'use_b64'   => $this->options['use_b64'],
                  // 'transport' => self::TRANSPORT_WEBSOCKET);
        // $url = sprintf('/%s/?%s', trim($this->url['path'], '/'), http_build_query($query));
		$url = '/socket.io/1/websocket/X6rc4QXGbyQWJT1XDxIU';
        $key = base64_encode(sha1(uniqid(mt_rand(), true), true));
        $request = "GET {$url} HTTP/1.1\r\n"
                 . "Host: {$this->url['host']}\r\n"
                 . "Upgrade: WebSocket\r\n"
                 . "Connection: Upgrade\r\n"
                 . "Sec-WebSocket-Key: {$key}\r\n"
                 . "Sec-WebSocket-Version: 13\r\n"
                 . "Origin: *\r\n\r\n";
        
		
		fwrite($this->stream, $request);
        $result = fread($this->stream, 12);
		
        if ('HTTP/1.1 101' !== $result) {
            throw new Exception(sprintf('The server returned an unexpected value. Expected "HTTP/1.1 101", had "%s"', $result));
        }
        // cleaning up the stream
        while ('' !== trim(fgets($this->stream)));
        $this->write(self::UPGRADE);
    }
}
