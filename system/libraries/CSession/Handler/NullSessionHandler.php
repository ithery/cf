<?php

class CSession_Handler_NullSessionHandler implements SessionHandlerInterface {
    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function open($savePath, $sessionName) {
        return true;
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function close() {
        return true;
    }

    /**
     * @inheritdoc
     *
     * @return string|false
     */
    public function read($sessionId) {
        return '';
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function write($sessionId, $data) {
        return true;
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function destroy($sessionId) {
        return true;
    }

    /**
     * @inheritdoc
     *
     * @return int|false
     */
    public function gc($lifetime) {
        return true;
    }
}
