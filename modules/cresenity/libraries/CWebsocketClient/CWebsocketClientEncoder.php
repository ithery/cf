<?php
/**
 * Encode the payload before sending it to a frame
 *
 * Based on the work of the following :
 *   - Ludovic Barreca (@ludovicbarreca), project founder
 *   - Byeoung Wook (@kbu1564) in #49
 *
 * @orignal author Baptiste Clavié <baptiste@wisembly.com>
 */
 
class CWebsocketClientEncoder extends CWebsocketClientAbstractPayload
{
    private $data;
    private $payload;
    /**
     * @param string  $data   data to encode
     * @param integer $opcode OpCode to use (one of AbstractPayload's constant)
     * @param bool    $mask   Should we use a mask ?
     */
    public function __construct($data, $op_code, $mask)
    {
        $this->data    = $data;
        $this->op_code  = $op_code;
        $this->mask    = (bool) $mask;
        if (true === $this->mask) {
            $this->mask_key = $this->generateRandomdMT(4);
        }
    }
	public function generateRandomdMT($length) {
	   $validCharacters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	   $myKeeper = '';
	   for ($n = 1; $n < $length; $n++) {
		  $whichCharacter = mt_rand(0, strlen($validCharacters)-1);
		  $myKeeper .= $validCharacters{$whichCharacter};
	   }
	   return $myKeeper;
	}
	public function openssl_random_pseudo_bytes($length) {
        $length_n = (int) $length; // shell injection is no fun
        $handle = popen("/usr/bin/openssl rand $length_n", "r");
        $data = stream_get_contents($handle);
        pclose($handle);
        return $data;
    }
	public function encode()
    {
        if (null !== $this->payload) {
            return;
        }
        $pack   = '';
        $length = strlen($this->data);
        if (0xFFFF < $length) {
            $pack   = pack('NN', ($length & 0xFFFFFFFF00000000) >> bindec(100000), $length & 0x00000000FFFFFFFF);
            $length = 0x007F;
        } elseif (0x007D < $length) {
            $pack   = pack('n*', $length);
            $length = 0x007E;
        }
        $payload = ($this->fin << 1) | $this->rsv[0];
        $payload = ($payload   << 1) | $this->rsv[1];
        $payload = ($payload   << 1) | $this->rsv[2];
        $payload = ($payload   << 4) | $this->op_code;
        $payload = ($payload   << 1) | $this->mask;
        $payload = ($payload   << 7) | $length;
        $data    = $this->data;
        $payload = pack('n', $payload) . $pack;
        if (true === $this->mask) {
            $payload .= $this->mask_key;
            $data     = $this->mask_data($data);
        }
        $this->payload = $payload . $data;
    }
    public function __toString() {
        $this->encode();
        return $this->payload;
    }
}