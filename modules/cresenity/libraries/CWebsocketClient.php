<?php

define('CWS_EVENT', 2);
define('CWS_BINARY_EVENT', 5);
class CWebsocketClient {
	
	private $_socket = null;
	private $_host = null;
	private $_port = null;
	private $_path = null;
	private $_origin = null;
	private $_redis = null;
	private $_connected = false;
	private $_debug = true;
	
	private $_key_type='socket.io';
	protected function __construct() {
		
	}
	
	private function _debug($var) {
		if($this->_debug) {
			cdbg::var_dump($var);
		}
	}
	
	public static function factory() {
		return new CWebsocketClient();
	}
	
	public function check_connection() {
		$this->_connected = false;
		
		// send ping:
		$data = 'ping?';
		@fwrite($this->_socket, $this->_hybi10_decode($data, 'ping', true));
		$response = @fread($this->_socket, 300);
		if(empty($response))
		{			
			return false;
		}
		$response = $this->_hybi10_decode($response);
		if(!is_array($response))
		{			
			return false;
		}
		if(!isset($response['type']) || $response['type'] !== 'pong')
		{			
			return false;
		}
		$this->_connected = true;
		return true;
	}
	
	public function disconnect() {
		$this->_connected = false;
		is_resource($this->_socket) and fclose($this->_socket);
	}
	
	public function reconnect() {
		sleep(10);
		$this->_connected = false;
		fclose($this->_socket);
		$this->connect($this->_host, $this->_port, $this->_path, $this->_origin);		
	}
	public function emit() {
		$args = func_get_args();
		$this->_debug($args);
		$packet = array();
		$packet['type'] = CWS_EVENT;
		$packet['nsp'] = '/';
		$packet['data'] = $args;
		
		// publish
		
		$packed = $this->msgpack_pack(array($packet, array(
		  'rooms' => array(),
		  'flags' => array()
		)));
		$packed = $this->msgpack_pack($packet);
		$this->_debug('PACKED');
		$this->_debug($packed);
		echo $packed;
		// $args = array('PUBLISH',$packed);
        // $cmd = '*' . count($args) . "\r\n";
        // foreach ($args as $item) {
            // $cmd .= '$' . strlen($item) . "\r\n" . $item . "\r\n";
        // }
		
		$channel = 'socket.io#emitter';
		$cmd = 'PUBLISH '.$channel.' '.$packed;
        $cmd = 'PUBLISH '.$channel.' '.'Hello';
        fwrite($this->_socket, $cmd);
        $this->_debug($cmd);
		$response="";
		while (!feof($this->_socket)) {
			$response .= fgets($this->_socket, 128);
		}
		
		
		echo $response;
		$this->_debug($response);
		return true;
		
	  }
	public function send($data, $type = 'text', $masked = true) {
		if($this->_connected === false) {
			trigger_error("Not connected", E_USER_WARNING);
			return false;
		}
		if( !is_string($data)) {
			trigger_error("Not a string data was given.", E_USER_WARNING);
			return false;		
		}
		if (strlen($data) == 0)
		{
			return false;
		}
		$encoded_data = $this->_hybi10_encode($data, $type, $masked);
		$this->_debug($encoded_data);
		$res = @fwrite($this->_socket, $encoded_data);		
		if($res === 0 || $res === false)
		{
			return false;
		}		
		$this->_debug($res);
		$buffer = fread($this->_socket, 512);
		$this->_debug($buffer);
		$buffer = fread($this->_socket, 512);
		$this->_debug($buffer);
		
		$buffer = ' ';
		while($buffer !== '')
		{			
			 $buffer = fread($this->_socket, 512);// drop?
		}
		
		return true;
	}
	
	
	public function connect($host, $port, $path, $origin = false) {
		$this->_host = $host;
		$this->_port = $port;
		$this->_path = $path;
		$this->_origin = $origin;
		
		$key = base64_encode($this->_generate_random_string(16, false, true));				
		$header = "GET " . $path . " HTTP/1.1\r\n";
		$header.= "Host: ".$host.":".$port."\r\n";
		$header.= "Upgrade: websocket\r\n";
		$header.= "Connection: Upgrade\r\n";
		$header.= "Sec-WebSocket-Key: " . $key . "\r\n";
		$header.= "Sec-WebSocket-Extensions:permessage-deflate; client_max_window_bits";
		$header.= "Accept-Encoding:gzip, deflate, sdch\r\n";
		$header.= "Accept-Language:en-US,en;q=0.8\r\n";
		$header.= "Origin: http://kliktors.com\r\n";
		
			$header.= "Sec-WebSocket-Origin: " . $origin . "\r\n";
		
		$header.= "Sec-WebSocket-Version: 13\r\n\r\n";			
		$this->_debug($header);
		$this->_socket = fsockopen($host, $port, $errno, $errstr, 2);
		socket_set_timeout($this->_socket, 0, 10000);
		@fwrite($this->_socket, $header);
		$response="";
		while (!feof($this->_socket)) {
			$response .= fgets($this->_socket, 128);
		}
		$this->_debug('RESPONSE');
		
		$this->_debug($response);
		
		//$response = @fread($this->_socket, 1500);
		preg_match('#Sec-WebSocket-Accept:\s(.*)$#mU', $response, $matches);
		if ($matches) {
			$keyAccept = trim($matches[1]);
			$expectedResonse = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
			
			$this->_connected = ($keyAccept === $expectedResonse) ? true : false;
		}
		return $this->_connected;
	}
	private function _generate_random_string($length = 10, $addSpaces = true, $addNumbers = true) {  
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"§$%&/()=[]{}';
		$useChars = array();
		// select some random chars:    
		for($i = 0; $i < $length; $i++)
		{
			$useChars[] = $characters[mt_rand(0, strlen($characters)-1)];
		}
		// add spaces and numbers:
		if($addSpaces === true)
		{
			array_push($useChars, ' ', ' ', ' ', ' ', ' ', ' ');
		}
		if($addNumbers === true)
		{
			array_push($useChars, rand(0,9), rand(0,9), rand(0,9));
		}
		shuffle($useChars);
		$randomString = trim(implode('', $useChars));
		$randomString = substr($randomString, 0, $length);
		return $randomString;
	}
	
	private function _hybi10_encode($payload, $type = 'text', $masked = true)
	{
		$frameHead = array();
		$frame = '';
		$payloadLength = strlen($payload);
		
		switch($type)
		{		
			case 'text':
				// first byte indicates FIN, Text-Frame (10000001):
				$frameHead[0] = 129;				
			break;			
		
			case 'close':
				// first byte indicates FIN, Close Frame(10001000):
				$frameHead[0] = 136;
			break;
		
			case 'ping':
				// first byte indicates FIN, Ping frame (10001001):
				$frameHead[0] = 137;
			break;
		
			case 'pong':
				// first byte indicates FIN, Pong frame (10001010):
				$frameHead[0] = 138;
			break;
		}
		
		// set mask and payload length (using 1, 3 or 9 bytes) 
		if($payloadLength > 65535)
		{
			$payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
			$frameHead[1] = ($masked === true) ? 255 : 127;
			for($i = 0; $i < 8; $i++)
			{
				$frameHead[$i+2] = bindec($payloadLengthBin[$i]);
			}
			// most significant bit MUST be 0 (close connection if frame too big)
			if($frameHead[2] > 127)
			{
				$this->close(1004);
				return false;
			}
		}
		elseif($payloadLength > 125)
		{
			$payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
			$frameHead[1] = ($masked === true) ? 254 : 126;
			$frameHead[2] = bindec($payloadLengthBin[0]);
			$frameHead[3] = bindec($payloadLengthBin[1]);
		}
		else
		{
			$frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
		}
		// convert frame-head to string:
		foreach(array_keys($frameHead) as $i)
		{
			$frameHead[$i] = chr($frameHead[$i]);
		}
		if($masked === true)
		{
			// generate a random mask:
			$mask = array();
			for($i = 0; $i < 4; $i++)
			{
				$mask[$i] = chr(rand(0, 255));
			}
			
			$frameHead = array_merge($frameHead, $mask);			
		}						
		$frame = implode('', $frameHead);
		// append payload to frame:
		$framePayload = array();	
		for($i = 0; $i < $payloadLength; $i++)
		{		
			$frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
		}
		return $frame;
	}
	
	private function _hybi10_decode($data) {
		$payloadLength = '';
		$mask = '';
		$unmaskedPayload = '';
		$decodedData = array();
		
		// estimate frame type:
		$firstByteBinary = sprintf('%08b', ord($data[0]));		
		$secondByteBinary = sprintf('%08b', ord($data[1]));
		$opcode = bindec(substr($firstByteBinary, 4, 4));
		$isMasked = ($secondByteBinary[0] == '1') ? true : false;
		$payloadLength = ord($data[1]) & 127;		
		
		switch($opcode)
		{
			// text frame:
			case 1:
				$decodedData['type'] = 'text';				
			break;
		
			case 2:
				$decodedData['type'] = 'binary';
			break;
			
			// connection close frame:
			case 8:
				$decodedData['type'] = 'close';
			break;
			
			// ping frame:
			case 9:
				$decodedData['type'] = 'ping';				
			break;
			
			// pong frame:
			case 10:
				$decodedData['type'] = 'pong';
			break;
			
			default:
				return false;
			break;
		}
		
		if($payloadLength === 126)
		{
		   $mask = substr($data, 4, 4);
		   $payloadOffset = 8;
		   $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
		}
		elseif($payloadLength === 127)
		{
			$mask = substr($data, 10, 4);
			$payloadOffset = 14;
			$tmp = '';
			for($i = 0; $i < 8; $i++)
			{
				$tmp .= sprintf('%08b', ord($data[$i+2]));
			}
			$dataLength = bindec($tmp) + $payloadOffset;
			unset($tmp);
		}
		else
		{
			$mask = substr($data, 2, 4);	
			$payloadOffset = 6;
			$dataLength = $payloadLength + $payloadOffset;
		}	
		
		if($isMasked === true)
		{
			for($i = $payloadOffset; $i < $dataLength; $i++)
			{
				$j = $i - $payloadOffset;
				if(isset($data[$i]))
				{
					$unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
				}
			}
			$decodedData['payload'] = $unmaskedPayload;
		}
		else
		{
			$payloadOffset = $payloadOffset - 4;
			$decodedData['payload'] = substr($data, $payloadOffset);
		}
		
		return $decodedData;
	}
	private function msgpack_pack($input) {
		static $bigendian;
		if (!isset($bigendian)) $bigendian = (pack('S', 1) == pack('n', 1));
		// null
		if (is_null($input)) {
			return pack('C', 0xC0);
		}
		// booleans
		if (is_bool($input)) {
			return pack('C', $input ? 0xC3 : 0xC2);
		}
		// Integers
		if (is_int($input)) {
		// positive fixnum
		if (($input | 0x7F) == 0x7F) return pack('C', $input & 0x7F);
		// negative fixnum
		if ($input < 0 && $input >= -32) return pack('c', $input);
		// uint8
		if ($input > 0 && $input <= 0xFF) return pack('CC', 0xCC, $input);
		// uint16
		if ($input > 0 && $input <= 0xFFFF) return pack('Cn', 0xCD, $input);
		// uint32
		if ($input > 0 && $input <= 0xFFFFFFFF) return pack('CN', 0xCE, $input);
		// uint64
		if ($input > 0 && $input <= 0xFFFFFFFFFFFFFFFF) {
			// pack() does not support 64-bit ints, so pack into two 32-bits
			$h = ($input & 0xFFFFFFFF00000000) >> 32;
			$l = $input & 0xFFFFFFFF;
			return $bigendian ? pack('CNN', 0xCF, $l, $h) : pack('CNN', 0xCF, $h, $l);
		}
		// int8
		if ($input < 0 && $input >= -0x80) return pack('Cc', 0xD0, $input);
		// int16
		if ($input < 0 && $input >= -0x8000) {
			$p = pack('s', $input);
			return pack('Ca2', 0xD1, $bigendian ? $p : strrev($p));
		}
		// int32
		if ($input < 0 && $input >= -0x80000000) {
			$p = pack('l', $input);
			return pack('Ca4', 0xD2, $bigendian ? $p : strrev($p));
		}
		// int64
		if ($input < 0 && $input >= -0x8000000000000000) {
			// pack() does not support 64-bit ints either so pack into two 32-bits
			$p1 = pack('l', $input & 0xFFFFFFFF);
			$p2 = pack('l', ($input >> 32) & 0xFFFFFFFF);
			return $bigendian ? pack('Ca4a4', 0xD3, $p1, $p2) : pack('Ca4a4', 0xD3, strrev($p2), strrev($p1));
		}
		throw new Exception('Invalid integer: ' . $input);
		}
		// Floats
		if (is_float($input)) {
			// Just pack into a double, don't take any chances with single precision
			return pack('C', 0xCB) . ($bigendian ? pack('d', $input) : strrev(pack('d', $input)));
		}
		// Strings/Raw
		if (is_string($input)) {
			$len = strlen($input);
			if ($len < 32) {
			  return pack('Ca*', 0xA0 | $len, $input);
			} else if ($len <= 0xFFFF) {
			  return pack('Cna*', 0xDA, $len, $input);
			} else if ($len <= 0xFFFFFFFF) {
			  return pack('CNa*', 0xDB, $len, $input);
			} else {
			  throw new Exception('Input overflows (2^32)-1 byte max');
			}
		}
		// Arrays & Maps
		if (is_array($input)) {
			$keys = array_keys($input);
			$len = count($input);
			// Is this an associative array?
			$isMap = false;
			foreach ($keys as $key) {
			  if (!is_int($key)) {
				$isMap = true;
				break;
			  }
			}
			$buf = '';
			if ($len < 16) {
			  $buf .= pack('C', ($isMap ? 0x80 : 0x90) | $len);
			} else if ($len <= 0xFFFF) {
			  $buf .= pack('Cn', ($isMap ? 0xDE : 0xDC), $len);
			} else if ($len <= 0xFFFFFFFF) {
			  $buf .= pack('CN', ($isMap ? 0xDF : 0xDD), $len);
			} else {
			  throw new Exception('Input overflows (2^32)-1 max elements');
			}
			foreach ($input as $key => $elm) {
			  if ($isMap) $buf .= $this->msgpack_pack($key);
			  $buf .= $this->msgpack_pack($elm);
			}
			return $buf;
		}
		throw new Exception('Not able to pack/serialize input type: ' . gettype($input));
	}
	
}


