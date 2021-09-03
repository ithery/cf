<?php

/**
 * Description of CBoostrap
 *
 * @author Hery <hery@ittron.co.id>
 */
class CBootstrap {
    /**
     * @var CBootstrap
     */
    protected static $instance = null;

    /**
     * @var string[]
     */
    protected $bootsrapperClass = [];

    /**
     * @var string[]
     */
    protected $defaultBootstrapperClass = [
        CBootstrap_HandleExceptionBootstrapper::class
    ];

    /**
     * @var CBootstrap_BootstrapperAbstract[]
     */
    protected $bootstrapper;
    protected $booted;

    /**
     * @return CBootstrap
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CBootstrap();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->bootsrapper = [];
        $this->bootsrapperClass = [];
        $this->booted = false;
    }

    /**
     * @return array
     */
    protected function getBootstrapperClass() {
        return array_merge($this->defaultBootstrapperClass, $this->bootsrapperClass);
    }

    public function boot() {
        if (!$this->booted) {
            //we boot all bootstrapper
            $this->bootstrapper = c::collect($this->getBootstrapperClass())->map(function ($item) {
                return c::tap((new $item), function ($bootstrapper) {
                    $this->bootstrapper[] = $bootstrapper;
                    $bootstrapper->bootstrap();
                });
            })->all();

            $this->booted = true;
        }
    }

    public function addBootstrapper($class) {
        $classArr = carr::wrap($class);

        foreach ($classArr as $c) {
            $this->bootsrapperClass[] = $c;
        }
        return $this;
    }
}
