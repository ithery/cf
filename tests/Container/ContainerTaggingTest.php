<?php

use PHPUnit\Framework\TestCase;

class ContainerTaggingImplementationOne {
}

class ContainerTaggingImplementationTwo {
}

class ContainerTaggingImplementationThree {
}

class ContainerTaggingTest extends TestCase {
    /**
     * @return CContainer_Container
     */
    protected function makeContainer() {
        return new CContainer_Container();
    }

    public function testTagResolvesEveryMemberOfTheTag() {
        $container = $this->makeContainer();
        $container->tag(ContainerTaggingImplementationOne::class, 'kelompok');
        $container->tag(ContainerTaggingImplementationTwo::class, 'kelompok');

        $resolved = $container->tagged('kelompok');

        $this->assertCount(2, $resolved);
        $this->assertInstanceOf(ContainerTaggingImplementationOne::class, $resolved[0]);
        $this->assertInstanceOf(ContainerTaggingImplementationTwo::class, $resolved[1]);
    }

    public function testTagAcceptsAnArrayOfAbstractsAndOfTags() {
        $container = $this->makeContainer();
        $container->tag(
            [ContainerTaggingImplementationOne::class, ContainerTaggingImplementationTwo::class],
            ['kelompok', 'kelompok-lain']
        );

        $this->assertCount(2, $container->tagged('kelompok'));
        $this->assertCount(2, $container->tagged('kelompok-lain'));
    }

    public function testTagsAccumulateAcrossSeparateCalls() {
        $container = $this->makeContainer();
        $container->tag([ContainerTaggingImplementationOne::class], 'kelompok');
        $container->tag([ContainerTaggingImplementationTwo::class], 'kelompok');
        $container->tag([ContainerTaggingImplementationThree::class], 'kelompok');

        $this->assertCount(3, $container->tagged('kelompok'));
    }

    public function testAnUnknownTagResolvesToNothing() {
        $container = $this->makeContainer();

        $this->assertSame([], $container->tagged('tidak-pernah-didaftarkan'));
    }

    /**
     * A tag resolves through the container, so a shared binding stays shared.
     */
    public function testTaggedRespectsSharedBindings() {
        $container = $this->makeContainer();
        $container->singleton(ContainerTaggingImplementationOne::class);
        $container->tag([ContainerTaggingImplementationOne::class], 'kelompok');

        $first = $container->tagged('kelompok');
        $second = $container->tagged('kelompok');

        $this->assertSame($first[0], $second[0]);
    }

    public function testTaggedResolvesClosureBindings() {
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
}
