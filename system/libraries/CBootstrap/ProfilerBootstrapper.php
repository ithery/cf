<?php

/**
 * Description of ProfilerBootstrapper
 *
 * @author Hery
 */
class CBootstrap_ProfilerBootstrapper extends CBootstrap_BootstrapperAbstract {

    /**
     * Reserved memory so that errors can be displayed properly on memory exhaustion.
     *
     * @var string
     */
    public static $reservedMemory;

    /**
     * Bootstrap the given application.
     *
     * @return void
     */
    public function bootstrap() {

        self::$reservedMemory = str_repeat('x', 10240);



        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleShutdown() {
        if (!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalErrorFromPhpError($error, 0));
        }
    }

}
