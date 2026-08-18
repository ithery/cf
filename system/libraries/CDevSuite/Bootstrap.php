<?php

/**
 * Description of Bootstrap.
 */
class CDevSuite_Bootstrap {
    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * @var CCollection
     */
    protected $bootstrapper;

    /**
     * @var CDevSuite_Bootstrap
     */
    protected static $instance;

    /**
     * @return CDevSuite_Bootstrap
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return array
     */
    protected function getBootstrapperClass() {
        $bootstrapper = [];
        $bootstrapper[] = CDevSuite_Bootstrap_DevSuiteBootstrapper::class;
        switch (CServer::getOS()) {
            case CServer::OS_WINNT:
                $bootstrapper[] = CDevSuite_Bootstrap_WindowsBootstrapper_DependencyChecker::class;

                break;
            case CServer::OS_DARWIN:
                $bootstrapper[] = CDevSuite_Bootstrap_MacBootstrapper_DependencyChecker::class;

                break;
            case CServer::OS_LINUX:
                $bootstrapper[] = CDevSuite_Bootstrap_LinuxBootstrapper_DependencyChecker::class;

                break;
        }
        $bootstrapper[] = CDevSuite_Bootstrap_PruneBootstrapper::class;

        return $bootstrapper;
    }

    /**
     * Run all registered bootstrappers once.
     *
     * @return void
     */
    public function bootstrap() {
        if (!$this->booted) {
            $this->bootstrapper = c::collect($this->getBootstrapperClass())->map(function ($class) {
                return c::tap(new $class(), function ($bootstrapper) {
                    $bootstrapper->bootstrap();
                });
            });
            /*
             * Relocate config dir to ~/.config/devsuite/ if found in old location.
             */
            $this->booted = true;
        }
    }
}
