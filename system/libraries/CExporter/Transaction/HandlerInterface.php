<?php

interface CExporter_Transaction_HandlerInterface {
    /**
     * @param $callback
     *
     * @return mixed
     */
    public function __invoke($callback);
}
