<?php
use PHPUnit\Framework\TestCase;

class EngineResolverTest extends TestCase {
    public function testRegistersTheDefaultEnginesOnConstruction() {
        $resolver = new CView_EngineResolver();

        $this->assertInstanceOf(CView_Engine_FileEngine::class, $resolver->resolve('file'));
        $this->assertInstanceOf(CView_Engine_PhpEngine::class, $resolver->resolve('php'));
        $this->assertInstanceOf(CView_Engine_CompilerEngine::class, $resolver->resolve('blade'));
    }

    public function testResolveThrowsForAnUnregisteredEngine() {
        $resolver = new CView_EngineResolver();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Engine [unknown] not found.');

        $resolver->resolve('unknown');
    }

    public function testRegisterAddsACustomEngine() {
        $resolver = new CView_EngineResolver();
        $engine = new stdClass();
        $resolver->register('custom', function () use ($engine) {
            return $engine;
        });

        $this->assertSame($engine, $resolver->resolve('custom'));
    }

    public function testResolveCachesTheResolvedEngineInstance() {
        $resolver = new CView_EngineResolver();
        $calls = 0;
        $resolver->register('custom', function () use (&$calls) {
            $calls++;

            return new stdClass();
        });

        $first = $resolver->resolve('custom');
        $second = $resolver->resolve('custom');

        $this->assertSame($first, $second);
        $this->assertSame(1, $calls);
    }

    public function testReRegisteringAnEngineClearsItsCachedInstance() {
        $resolver = new CView_EngineResolver();
        $resolver->register('custom', function () {
            return 'first';
        });
        $resolver->resolve('custom');

        $resolver->register('custom', function () {
            return 'second';
        });

        $this->assertSame('second', $resolver->resolve('custom'));
    }
}
