<?php



/**
 * @internal
 */
trait CQC_Phpstan_Concern_HasContainer
{
    /**
     * @var ?\CContainer_ContainerInterface
     */
    protected $container;

    /**
     * @param  \CContainer_ContainerInterface  $container
     * @return void
     */
    public function setContainer(CContainer_ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * Returns the current broker.
     *
     * @return \CContainer_ContainerInterface
     */
    public function getContainer(): CContainer_ContainerInterface
    {
        return $this->container ?? CContainer_Container::getInstance();
    }

    /**
     * Resolve the given type from the container.
     *
     * @param  string  $abstract
     * @return mixed
     */
    public function resolve(string $abstract)
    {
        try {
            $concrete = $this->getContainer()->make($abstract);
        } catch (Throwable $ex) {
            return null;
        }

        return $concrete;
    }
}
