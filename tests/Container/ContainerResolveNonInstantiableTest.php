<?php

use PHPUnit\Framework\TestCase;

interface ContainerNonInstantiableContract {
}

abstract class ContainerNonInstantiableAbstract {
}

class ContainerNonInstantiablePrivateConstructor {
    private function __construct() {
    }
}

class ContainerNonInstantiableDependent {
    /**
     * @var ContainerNonInstantiableContract
     */
    public $impl;

    public function __construct(ContainerNonInstantiableContract $impl) {
        $this->impl = $impl;
    }
}

class ContainerNonInstantiableWithDefault {
    /**
     * @var null|ContainerNonInstantiableContract
     */
    public $impl;

    public function __construct(ContainerNonInstantiableContract $impl = null) {
        $this->impl = $impl;
    }
}

class ContainerResolveNonInstantiableTest extends TestCase {
    /**
     * @return CContainer_Container
     */
    protected function makeContainer() {
        return new CContainer_Container();
    }

    public function testAnInterfaceWithoutABindingThrows() {
        $container = $this->makeContainer();

        $this->expectException(CContainer_Exception_BindingResolutionException::class);
        $container->make(ContainerNonInstantiableContract::class);
    }

    public function testAnAbstractClassThrows() {
        $container = $this->makeContainer();

        $this->expectException(CContainer_Exception_BindingResolutionException::class);
        $container->make(ContainerNonInstantiableAbstract::class);
    }

    public function testAPrivateConstructorThrows() {
        $container = $this->makeContainer();

        $this->expectException(CContainer_Exception_BindingResolutionException::class);
        $container->make(ContainerNonInstantiablePrivateConstructor::class);
    }

    /**
     * The message has to name what was being built when it failed, otherwise a
     * missing binding deep in a graph gives no clue where to look.
     */
    public function testTheMessageNamesTheClassBeingBuilt() {
        $container = $this->makeContainer();

        try {
            $container->make(ContainerNonInstantiableDependent::class);
            $this->fail('Sebuah interface tanpa binding seharusnya menolak dibangun.');
        } catch (CContainer_Exception_BindingResolutionException $ex) {
            $this->assertTrue(strpos($ex->getMessage(), ContainerNonInstantiableContract::class) !== false, $ex->getMessage());
            $this->assertTrue(strpos($ex->getMessage(), ContainerNonInstantiableDependent::class) !== false, $ex->getMessage());
        }
    }

    public function testAMissingClassThrowsABindingResolutionException() {
        $container = $this->makeContainer();

        $this->expectException(CContainer_Exception_BindingResolutionException::class);
        $container->make('KelasIniTidakPernahAda');
    }

    /**
     * A nullable dependency the container cannot build falls back to its default
     * instead of failing.
     */
    public function testAnUnresolvableDependencyWithADefaultUsesTheDefault() {
        $container = $this->makeContainer();
        $resolved = $container->make(ContainerNonInstantiableWithDefault::class);

        $this->assertNull($resolved->impl);
    }

    public function testABoundInterfaceResolvesInsteadOfThrowing() {
        $container = $this->makeContainer();
        $container->bind(ContainerNonInstantiableContract::class, function () {
            return new class() implements ContainerNonInstantiableContract {
            };
        });
        $resolved = $container->make(ContainerNonInstantiableDependent::class);

        $this->assertInstanceOf(ContainerNonInstantiableContract::class, $resolved->impl);
    }
}
