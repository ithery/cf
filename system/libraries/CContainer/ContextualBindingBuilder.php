<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 4:48:43 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CContainer_ContextualBindingBuilder implements CContainer_ContextualBindingBuilderInterface {

    /**
     * The underlying container instance.
     *
     * @var CContainer_ContainerInterface
     */
    protected $container;

    /**
     * The concrete instance.
     *
     * @var string|array
     */
    protected $concrete;

    /**
     * The abstract target.
     *
     * @var string
     */
    protected $needs;

    /**
     * Create a new contextual binding builder.
     *
     * @param  CContainer_ContainerInterface  $container
     * @param  string|array  $concrete
     * @return void
     */
    public function __construct(Container $container, $concrete) {
        $this->concrete = $concrete;
        $this->container = $container;
    }

    /**
     * Define the abstract target that depends on the context.
     *
     * @param  string  $abstract
     * @return $this
     */
    public function needs($abstract) {
        $this->needs = $abstract;
        return $this;
    }

    /**
     * Define the implementation for the contextual binding.
     *
     * @param  \Closure|string  $implementation
     * @return void
     */
    public function give($implementation) {
        foreach (Arr::wrap($this->concrete) as $concrete) {
            $this->container->addContextualBinding($concrete, $this->needs, $implementation);
        }
    }

}
