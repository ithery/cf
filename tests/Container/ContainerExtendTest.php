<?php

use PHPUnit\Framework\TestCase;

class ContainerExtendStub {
    /**
     * @var string
     */
    public $tanda = '';
}

class ContainerExtendDecorator {
    /**
     * @var mixed
     */
    public $inner;

    public function __construct($inner) {
        $this->inner = $inner;
    }
}

class ContainerExtendTest extends TestCase {
    /**
     * @return CContainer_Container
     */
    protected function makeContainer() {
        return new CContainer_Container();
    }

    public function testExtendRunsAfterTheBinding() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'Cresenity';
        });
        $container->extend('nama', function ($value) {
            return $value . ' Framework';
        });

        $this->assertSame('Cresenity Framework', $container->make('nama'));
    }

    public function testExtendersRunInTheOrderTheyWereAdded() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'a';
        });
        $container->extend('nama', function ($value) {
            return $value . 'b';
        });
        $container->extend('nama', function ($value) {
            return $value . 'c';
        });

        $this->assertSame('abc', $container->make('nama'));
    }

    public function testExtendReceivesTheContainerAsSecondArgument() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'Cresenity';
        });
        $seen = null;
        $container->extend('nama', function ($value, $c) use (&$seen) {
            $seen = $c;

            return $value;
        });
        $container->make('nama');

        $this->assertSame($container, $seen);
    }

    public function testExtendCanReplaceTheObjectEntirely() {
        $container = $this->makeContainer();
        $container->bind(ContainerExtendStub::class);
        $container->extend(ContainerExtendStub::class, function ($value) {
            return new ContainerExtendDecorator($value);
        });
        $resolved = $container->make(ContainerExtendStub::class);

        $this->assertInstanceOf(ContainerExtendDecorator::class, $resolved);
        $this->assertInstanceOf(ContainerExtendStub::class, $resolved->inner);
    }

    public function testExtendAppliesToASingletonOnlyOnce() {
        $container = $this->makeContainer();
        $container->singleton('nama', function () {
            return 'a';
        });
        $container->extend('nama', function ($value) {
            return $value . 'b';
        });

        $this->assertSame('ab', $container->make('nama'));
        $this->assertSame('ab', $container->make('nama'));
    }

    public function testExtendRunsEveryTimeForAPlainBinding() {
        $container = $this->makeContainer();
        $count = 0;
        $container->bind('nama', function () {
            return 'a';
        });
        $container->extend('nama', function ($value) use (&$count) {
            $count++;

            return $value;
        });
        $container->make('nama');
        $container->make('nama');

        $this->assertSame(2, $count);
    }

    public function testExtendAfterAnInstanceIsRegisteredAppliesImmediately() {
        $container = $this->makeContainer();
        $container->instance('nama', 'a');
        $container->extend('nama', function ($value) {
            return $value . 'b';
        });

        $this->assertSame('ab', $container->make('nama'));
    }

    public function testExtendBeforeTheBindingExistsStillApplies() {
        $container = $this->makeContainer();
        $container->extend('nama', function ($value) {
            return $value . 'b';
        });
        $container->bind('nama', function () {
            return 'a';
        });

        $this->assertSame('ab', $container->make('nama'));
    }

    public function testForgetExtendersDropsThemBeforeResolution() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'a';
        });
        $container->extend('nama', function ($value) {
            return $value . 'b';
        });
        $container->forgetExtenders('nama');

        $this->assertSame('a', $container->make('nama'));
    }

    /**
     * Once a singleton is built the extender has already been folded in, so
     * forgetting it afterwards changes nothing until the instance is dropped.
     */
    public function testForgetExtendersDoesNotUnwindAnAlreadyBuiltSingleton() {
        $container = $this->makeContainer();
        $container->singleton('nama', function () {
            return 'a';
        });
        $container->extend('nama', function ($value) {
            return $value . 'b';
        });
        $this->assertSame('ab', $container->make('nama'));

        $container->forgetExtenders('nama');
        $this->assertSame('ab', $container->make('nama'));

        $container->forgetInstance('nama');
        $this->assertSame('a', $container->make('nama'));
    }

    public function testExtendResolvesThroughAnAlias() {
        $container = $this->makeContainer();
        $container->bind('nama', function () {
            return 'a';
        });
        $container->alias('nama', 'sebutan');
        $container->extend('sebutan', function ($value) {
            return $value . 'b';
        });

        $this->assertSame('ab', $container->make('nama'));
    }
}
