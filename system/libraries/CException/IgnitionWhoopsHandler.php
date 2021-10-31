<?php

use Whoops\Handler\Handler;

class CException_IgnitionWhoopsHandler extends Handler {
    protected $errorPageHandler;

    protected $exception;

    public function __construct(ErrorPageRenderer $errorPageHandler) {
        $this->errorPageHandler = $errorPageHandler;
    }

    public function handle() {
        try {
            $this->errorPageHandler->render($this->exception);
        } catch (Error $error) {
            // Errors aren't caught by Whoops.
            // Convert the error to an exception and throw again.

            throw new ErrorException(
                $error->getMessage(),
                $error->getCode(),
                1,
                $error->getFile(),
                $error->getLine(),
                $error
            );
        }

        return Handler::QUIT;
    }

    /**
     * @param \Throwable $exception
     */
    public function setException($exception) {
        $this->exception = $exception;
    }
}
