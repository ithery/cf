<?php
class CWebSocket_Exception extends Exception {
    /**
     * Get the payload, Pusher-like formatted.
     *
     * @return array
     */
    public function getPayload() {
        return [
            'event' => 'pusher:error',
            'data' => [
                'message' => $this->getMessage(),
                'code' => $this->getCode(),
            ],
        ];
    }

    /**
     * Trigger the exception message.
     *
     * @param string $message
     * @param int    $code
     *
     * @return void
     */
    public function trigger($message, $code = 4001) {
        $this->message = $message;
        $this->code = $code;
    }
}
