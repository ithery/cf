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
 
class CWebsocketClientDecoder extends CWebsocketClientAbstractPayload
{
	private $payload;
    private $data;
    private $length;
    /** @param string $payload Payload to decode */
    public function __construct($payload) {
        $this->payload = $payload;
    }
    public function decode() {
        if (null !== $this->data) {
            return;
        }
        $length = $this->count();
        // if ($payload !== null) and ($payload packet error)?
        // invalid websocket packet data or not (text, binary opCode)
		
        if (3 > $length) {
            return;
        }
        $payload = array_map('ord', str_split($this->payload));
        $this->fin = ($payload[0] >> 7);
        $this->rsv = array(($payload[0] >> 6) & 1,  // rsv1
                      ($payload[0] >> 5) & 1,  // rsv2
                      ($payload[0] >> 4) & 1); // rsv3
        $this->opCode = $payload[0] & 0xF;
        $this->mask   = (bool) ($payload[1] >> 7);
        $payload_offset = 2;
        if ($length > 125) {
            $payload_offset = (0xFFFF < $length && 0xFFFFFFFF >= $length) ? 6 : 4;
        }
        $payload = implode('', array_map('chr', $payload));
        if (true === $this->mask) {
            $this->mask_key  = substr($payload, $payload_offset, 4);
            $payload_offset += 4;
        }
        $data = substr($payload, $payload_offset, $length);
		
        if (true === $this->mask) {
            $data = $this->mask_data($data);
        }
        $this->data = $data;
    }
    public function count() {
        if (null === $this->payload) {
            return 0;
        }
        if (null !== $this->length) {
            return $this->length;
        }
        $length = ord($this->payload[1]) & 0x7F;
        if ($length == 126 || $length == 127) {
            $length = unpack('H*', substr($this->payload, 2, ($length == 126 ? 2 : 4)));
            $length = hexdec($length[1]);
        }
        return $this->length = $length;
    }
    public function __toString() {
        $this->decode();
        return $this->data ?$this->data: '';
    }
}