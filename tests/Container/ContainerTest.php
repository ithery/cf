<?php

use PHPUnit\Framework\TestCase;

interface ContainerTestContractStub {
}

class ContainerTestConcreteStub implements ContainerTestContractStub {
}

class ContainerTestSecondConcreteStub implements ContainerTestContractStub {
}

class ContainerTestNoConstructor {
}

class ContainerTestDependent {
    /**
     * @var ContainerTestConcreteStub
     */
    public $stub;

    public function __construct(ContainerTestConcreteStub $stub) {
        $this->stub = $stub;
    }
}

class ContainerTestNestedDependent {
    /**
     * @var ContainerTestDependent
     */
    public $inner;

    public function __construct(ContainerTestDependent $inner) {
        $this->inner = $inner;
    }
}

class ContainerTestDefaultValue {
    /**
     * @var ContainerTestConcreteStub
     */
    public $stub;

    /**
     * @var string
     */
    public $default;

    public function __construct(ContainerTestConcreteStub $stub, $default = 'kosong') {
        $this->stub = $stub;
        $this->default = $default;
    }
}

class ContainerTestContractDependent {
    /**
     * @var ContainerTestContractStub
     */
    public $impl;

    public function __construct(ContainerTestContractStub $impl) {
        $this->impl = $impl;
    }
}

abstract class ContainerTestAbstractStub {
}

class ContainerTestMethodStub {
    /**
     * @return string
     */
    public function work() {
        return 'bekerja';
    }

    /**
     * @param ContainerTestConcreteStub $stub
     *
     * @return ContainerTestConcreteStub
     */
    public function inject(ContainerTestConcreteStub $stub) {
        return $stub;
    }
}

class ContainerTest extends TestCase {
    /**
     * @return CContainer_Container
     */
    protected function makeContainer() {
        return new CContainer_Container();
    }

    public function testClosureResolutionReturnsTheClosureResult() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'Cresenity';
        });

        $this->assertSame('Cresenity', $container->make('nama'));
    }

    public function testBindIfDoesNotOverrideAnExistingBinding() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'pertama';
        });
        $container->bindIf('nama', function () {
            return 'kedua';
        });

        $this->assertSame('pertama', $container->make('nama'));
    }

    public function testBindIfRegistersWhenNothingIsBoundYet() {
        $container = $this->makeContainer();
        $container->bindIf('nama', function () {
            return 'pertama';
        });

        $this->assertSame('pertama', $container->make('nama'));
    }

    /**
     * A plain binding hands back a new object every time; that is the whole
     * difference between bind() and singleton().
     */
    public function testSharedBindingsReturnTheSameInstance() {
        $container = $this->makeContainer();
        $container->singleton(ContainerTestConcreteStub::class);

        $this->assertSame($container->make(ContainerTestConcreteStub::class), $container->make(ContainerTestConcreteStub::class));
    }

    public function testPlainBindingsReturnAFreshInstanceEachTime() {
        $container = $this->makeContainer();
        $container->bind(ContainerTestConcreteStub::class);

        $this->assertNotSame($container->make(ContainerTestConcreteStub::class), $container->make(ContainerTestConcreteStub::class));
    }

    public function testInstanceRegistersAnExistingObject() {
        $container = $this->makeContainer();
        $object = new ContainerTestConcreteStub();
        $container->instance('stub', $object);

        $this->assertSame($object, $container->make('stub'));
        $this->assertTrue($container->bound('stub'));
    }

    public function testAliasResolvesToTheSameBinding() {
        $container = $this->makeContainer();
        $container->singleton(ContainerTestConcreteStub::class);
        $container->alias(ContainerTestConcreteStub::class, 'stub');

        $this->assertSame($container->make('stub'), $container->make(ContainerTestConcreteStub::class));
        $this->assertTrue($container->isAlias('stub'));
        $this->assertSame(ContainerTestConcreteStub::class, $container->getAlias('stub'));
    }

    public function testAConcreteClassIsBuiltWithoutBeingBound() {
        $container = $this->makeContainer();

        $this->assertInstanceOf(ContainerTestNoConstructor::class, $container->make(ContainerTestNoConstructor::class));
        $this->assertFalse($container->bound(ContainerTestNoConstructor::class));
    }

    public function testConstructorDependenciesAreInjected() {
        $container = $this->makeContainer();
        $resolved = $container->make(ContainerTestDependent::class);

        $this->assertInstanceOf(ContainerTestDependent::class, $resolved);
        $this->assertInstanceOf(ContainerTestConcreteStub::class, $resolved->stub);
    }

    public function testNestedDependenciesAreInjected() {
        $container = $this->makeContainer();
        $resolved = $container->make(ContainerTestNestedDependent::class);

        $this->assertInstanceOf(ContainerTestDependent::class, $resolved->inner);
        $this->assertInstanceOf(ContainerTestConcreteStub::class, $resolved->inner->stub);
    }

    public function testDefaultParameterValuesAreHonoured() {
        $container = $this->makeContainer();
        $resolved = $container->make(ContainerTestDefaultValue::class);

        $this->assertSame('kosong', $resolved->default);
    }

    public function testMakeWithOverridesADefaultParameter() {
        $container = $this->makeContainer();
        $resolved = $container->makeWith(ContainerTestDefaultValue::class, ['default' => 'diisi']);

        $this->assertSame('diisi', $resolved->default);
    }

    public function testAnInterfaceResolvesToItsBoundImplementation() {
        $container = $this->makeContainer();
        $container->bind(ContainerTestContractStub::class, ContainerTestConcreteStub::class);
        $resolved = $container->make(ContainerTestContractDependent::class);

        $this->assertInstanceOf(ContainerTestConcreteStub::class, $resolved->impl);
    }

    public function testExtendWrapsTheResolvedObject() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'Cresenity';
        });
        $container->extend('nama', function ($value) {
            return $value . ' Framework';
        });

        $this->assertSame('Cresenity Framework', $container->make('nama'));
    }

    public function testExtendAppliesToAnAlreadyRegisteredInstance() {
        $container = $this->makeContainer();
        $container->instance('nama', 'Cresenity');
        $container->extend('nama', function ($value) {
            return $value . ' Framework';
        });

        $this->assertSame('Cresenity Framework', $container->make('nama'));
    }

    public function testTaggedReturnsEveryTaggedBinding() {
        $container = $this->makeContainer();
        $container->bind('satu', function () {
            return 'a';
        });
        $container->bind('dua', function () {
            return 'b';
        });
        $container->tag(['satu', 'dua'], 'huruf');

        $this->assertSame(['a', 'b'], $container->tagged('huruf'));
    }

    public function testTaggedIsEmptyForAnUnknownTag() {
        $container = $this->makeContainer();

        $this->assertSame([], $container->tagged('tidak-ada'));
    }

    public function testResolvingCallbacksFireOnEveryResolution() {
        $container = $this->makeContainer();
        $seen = 0;
        $container->resolving(ContainerTestConcreteStub::class, function () use (&$seen) {
            $seen++;
        });
        $container->make(ContainerTestConcreteStub::class);
        $container->make(ContainerTestConcreteStub::class);

        $this->assertSame(2, $seen);
    }

    public function testAfterResolvingCallbacksFire() {
        $container = $this->makeContainer();
        $seen = null;
        $container->afterResolving(ContainerTestConcreteStub::class, function ($object) use (&$seen) {
            $seen = $object;
        });
        $resolved = $container->make(ContainerTestConcreteStub::class);

        $this->assertSame($resolved, $seen);
    }

    /**
     * Two implementations of one contract, chosen by who is asking for it.
     */
    public function testContextualBindingPicksTheImplementationPerConsumer() {
        $container = $this->makeContainer();
        $container->bind(ContainerTestContractStub::class, ContainerTestConcreteStub::class);
        $container->when(ContainerTestContractDependent::class)
            ->needs(ContainerTestContractStub::class)
            ->give(ContainerTestSecondConcreteStub::class);

        $resolved = $container->make(ContainerTestContractDependent::class);

        $this->assertInstanceOf(ContainerTestSecondConcreteStub::class, $resolved->impl);
        $this->assertInstanceOf(ContainerTestConcreteStub::class, $container->make(ContainerTestContractStub::class));
    }

    public function testCallInvokesAMethodAndInjectsItsDependencies() {
        $container = $this->makeContainer();

        $this->assertSame('bekerja', $container->call([new ContainerTestMethodStub(), 'work']));
        $this->assertInstanceOf(
            ContainerTestConcreteStub::class,
            $container->call([new ContainerTestMethodStub(), 'inject'])
        );
    }

    public function testCallInvokesAClosureAndInjectsItsDependencies() {
        $container = $this->makeContainer();
        $resolved = $container->call(function (ContainerTestConcreteStub $stub) {
            return $stub;
        });

        $this->assertInstanceOf(ContainerTestConcreteStub::class, $resolved);
    }

    public function testBoundAndHasAgreeOnWhatIsRegistered() {
        $container = $this->makeContainer();

        $this->assertFalse($container->bound('nama'));
        $this->assertFalse($container->has('nama'));

        $container->bind('nama', function () {
            return 'Cresenity';
        });

        $this->assertTrue($container->bound('nama'));
        $this->assertTrue($container->has('nama'));
    }

    public function testForgetInstanceDropsOnlyTheResolvedCopy() {
        $container = $this->makeContainer();
        $container->singleton(ContainerTestConcreteStub::class);
        $first = $container->make(ContainerTestConcreteStub::class);
        $container->forgetInstance(ContainerTestConcreteStub::class);

        $this->assertNotSame($first, $container->make(ContainerTestConcreteStub::class));
        $this->assertTrue($container->bound(ContainerTestConcreteStub::class));
    }

    public function testFlushClearsEverything() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'Cresenity';
        });
        $container->instance('stub', new ContainerTestConcreteStub());
        $container->flush();

        $this->assertFalse($container->bound('nama'));
        $this->assertFalse($container->bound('stub'));
        $this->assertSame([], $container->getBindings());
    }

    public function testResolvedReportsWhetherABindingHasBeenBuilt() {
        $container = $this->makeContainer();
        $container->bind(ContainerTestConcreteStub::class);

        $this->assertFalse($container->resolved(ContainerTestConcreteStub::class));
        $container->make(ContainerTestConcreteStub::class);
        $this->assertTrue($container->resolved(ContainerTestConcreteStub::class));
    }

    /**
     * An abstract class cannot be built, and the container has to say so rather
     * than fail somewhere further down.
     */
    public function testBuildingAnAbstractClassThrows() {
        $container = $this->makeContainer();

        $this->expectException(CContainer_Exception_BindingResolutionException::class);
        $container->make(ContainerTestAbstractStub::class);
    }

    public function testResolvingAnUnknownStringThrows() {
        $container = $this->makeContainer();

        $this->expectException(CContainer_Exception_BindingResolutionException::class);
        $container->make('TidakAdaKelasSepertiIni');
    }

    public function testFactoryReturnsACallableThatResolves() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'Cresenity';
        });
        $factory = $container->factory('nama');

        $this->assertSame('Cresenity', $factory());
    }

    public function testArrayAccessMirrorsBindAndMake() {
        $container = $this->makeContainer();
        $container['nama'] = function () {
            return 'Cresenity';
        };

        $this->assertTrue(isset($container['nama']));
        $this->assertSame('Cresenity', $container['nama']);

        unset($container['nama']);
        $this->assertFalse(isset($container['nama']));
    }
}
