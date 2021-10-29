<?php

interface CException_Contract_ExceptionRendererInterface {
    /**
     * Renders the given exception as HTML.
     *
     * @param \Throwable $throwable
     *
     * @return string
     */
    public function render($throwable);
}
