<?php
class CExporter_Transaction_Handler_NullHandler implements CExporter_Transaction_HandlerInterface {
    /**
     * @param $callback
     *
     * @return mixed
     */
    public function __invoke($callback) {
        return $callback();
    }
}
