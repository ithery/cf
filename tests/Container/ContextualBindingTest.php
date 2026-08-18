<?php

use PHPUnit\Framework\TestCase;

interface ContextualLoggerContract {
}

class ContextualFileLogger implements ContextualLoggerContract {
}

class ContextualDatabaseLogger implements ContextualLoggerContract {
}

class ContextualNullLogger implements ContextualLoggerContract {
}

class ContextualConsumerOne {
    /**
     * @var ContextualLoggerContract
     */
    public $logger;

    public function __construct(ContextualLoggerContract $logger) {
        $this->logger = $logger;
    }
}

class ContextualConsumerTwo {
    /**
     * @var ContextualLoggerContract
     */
    public $logger;

    public function __construct(ContextualLoggerContract $logger) {
        $this->logger = $logger;
    }
}

class ContextualPrimitiveConsumer {
    /**
     * @var string
     */
    public $nama;

    public function __construct($nama) {
        $this->nama = $nama;
    }
}

class ContextualNestedConsumer {
    /**
     * @var ContextualConsumerOne
     */
    public $inner;

    public function __construct(ContextualConsumerOne $inner) {
        $this->inner = $inner;
    }
}

class ContextualBindingTest extends TestCase {
    /**
     * @return CContainer_Container
     */
    protected function makeContainer() {
        return new CContainer_Container();
    }

    public function testGiveAConcreteClassName() {
        $container = $this->makeContainer();
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualFileLogger::class);

        $this->assertInstanceOf(ContextualFileLogger::class, $container->make(ContextualConsumerOne::class)->logger);
    }

    public function testGiveAClosure() {
        $container = $this->makeContainer();
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(function () {
                return new ContextualDatabaseLogger();
            });

        $this->assertInstanceOf(ContextualDatabaseLogger::class, $container->make(ContextualConsumerOne::class)->logger);
    }

    public function testTheClosureReceivesTheContainer() {
        $container = $this->makeContainer();
        $seen = null;
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(function ($c) use (&$seen) {
                $seen = $c;

                return new ContextualFileLogger();
            });
        $container->make(ContextualConsumerOne::class);

        $this->assertSame($container, $seen);
    }

    public function testTwoConsumersEachGetTheirOwnImplementation() {
        $container = $this->makeContainer();
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualFileLogger::class);
        $container->when(ContextualConsumerTwo::class)
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualDatabaseLogger::class);

        $this->assertInstanceOf(ContextualFileLogger::class, $container->make(ContextualConsumerOne::class)->logger);
        $this->assertInstanceOf(ContextualDatabaseLogger::class, $container->make(ContextualConsumerTwo::class)->logger);
    }

    public function testAContextualBindingOverridesAGlobalOne() {
        $container = $this->makeContainer();
        $container->bind(ContextualLoggerContract::class, ContextualNullLogger::class);
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualFileLogger::class);

        $this->assertInstanceOf(ContextualFileLogger::class, $container->make(ContextualConsumerOne::class)->logger);
        $this->assertInstanceOf(ContextualNullLogger::class, $container->make(ContextualLoggerContract::class));
    }

    public function testAConsumerWithoutAContextualBindingFallsBackToTheGlobalOne() {
        $container = $this->makeContainer();
        $container->bind(ContextualLoggerContract::class, ContextualNullLogger::class);
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualFileLogger::class);

        $this->assertInstanceOf(ContextualNullLogger::class, $container->make(ContextualConsumerTwo::class)->logger);
    }

    public function testWhenAcceptsSeveralConsumersAtOnce() {
        $container = $this->makeContainer();
        $container->when([ContextualConsumerOne::class, ContextualConsumerTwo::class])
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualFileLogger::class);

        $this->assertInstanceOf(ContextualFileLogger::class, $container->make(ContextualConsumerOne::class)->logger);
        $this->assertInstanceOf(ContextualFileLogger::class, $container->make(ContextualConsumerTwo::class)->logger);
    }

    public function testAPrimitiveDependencyCanBeGivenAValue() {
        $container = $this->makeContainer();
        $container->when(ContextualPrimitiveConsumer::class)
            ->needs('$nama')
            ->give('Cresenity');

        $this->assertSame('Cresenity', $container->make(ContextualPrimitiveConsumer::class)->nama);
    }

    public function testAPrimitiveDependencyCanBeGivenAClosure() {
        $container = $this->makeContainer();
        $container->when(ContextualPrimitiveConsumer::class)
            ->needs('$nama')
            ->give(function () {
                return 'dari closure';
            });

        $this->assertSame('dari closure', $container->make(ContextualPrimitiveConsumer::class)->nama);
    }

    public function testAContextualBindingAppliesWhenNestedInsideAnotherBuild() {
        $container = $this->makeContainer();
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualFileLogger::class);
        $resolved = $container->make(ContextualNestedConsumer::class);

        $this->assertInstanceOf(ContextualFileLogger::class, $resolved->inner->logger);
    }

    public function testGiveAnExistingInstanceThroughAClosure() {
        $container = $this->makeContainer();
        $shared = new ContextualFileLogger();
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(function () use ($shared) {
                return $shared;
            });

        $this->assertSame($shared, $container->make(ContextualConsumerOne::class)->logger);
    }

    public function testTheLastContextualBindingWins() {
        $container = $this->makeContainer();
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualFileLogger::class);
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualDatabaseLogger::class);

        $this->assertInstanceOf(ContextualDatabaseLogger::class, $container->make(ContextualConsumerOne::class)->logger);
    }

    /**
     * A contextual binding is per resolution, not a one-off, so a second build
     * gets the same treatment as the first.
     */
    public function testTheContextualBindingSurvivesRepeatedResolution() {
        $container = $this->makeContainer();
        $container->when(ContextualConsumerOne::class)
            ->needs(ContextualLoggerContract::class)
            ->give(ContextualFileLogger::class);

        $this->assertInstanceOf(ContextualFileLogger::class, $container->make(ContextualConsumerOne::class)->logger);
        $this->assertInstanceOf(ContextualFileLogger::class, $container->make(ContextualConsumerOne::class)->logger);
    }

    public function testAddContextualBindingRegistersDirectly() {
        $container = $this->makeContainer();
        $container->addContextualBinding(
            ContextualConsumerOne::class,
            ContextualLoggerContract::class,
            ContextualDatabaseLogger::class
        );

        $this->assertInstanceOf(ContextualDatabaseLogger::class, $container->make(ContextualConsumerOne::class)->logger);
    }
}
