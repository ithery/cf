<?php
/**
 * Class EvalJsException.
 */
class CApp_React_Exception_EvalJsException extends \RuntimeException {
    /**
     * EvalJsException constructor.
     *
     * @param string $componentName
     * @param int    $consoleReplay
     */
    public function __construct($componentName, $consoleReplay) {
        $message = 'Error rendering component ' . $componentName . "\nConsole log:" . $consoleReplay;
        parent::__construct($message);
    }
}
