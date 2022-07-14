<?php
trait CBot_Trait_HandlesExceptionsTrait {
    /**
     * Register a custom exception handler.
     *
     * @param string   $exception
     * @param callable $closure
     */
    public function exception($exception, $closure) {
        $this->exceptionHandler->register($exception, $this->getCallable($closure));
    }

    /**
     * @param CBot_Contract_ExceptionHandlerInterface $exceptionHandler
     */
    public function setExceptionHandler(CBot_Contract_ExceptionHandlerInterface $exceptionHandler) {
        $this->exceptionHandler = $exceptionHandler;
    }
}
