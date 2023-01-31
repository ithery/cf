<?php

class CImage_Graph_Exception extends Exception {
    /**
     * @param string $message
     *
     * @return CImage_Graph_Exception_InvalidArgumentException
     */
    public static function invalidArgument($message) {
        return new CImage_Graph_Exception_InvalidArgumentException($message);
    }
}
