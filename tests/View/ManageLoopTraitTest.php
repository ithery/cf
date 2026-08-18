<?php
use PHPUnit\Framework\TestCase;

class ManageLoopTraitTest extends TestCase {
    protected function factory() {
        return new CView_Factory();
    }

    public function testAddLoopInitializesTheFirstLoopFrame() {
        $factory = $this->factory();

        $factory->addLoop(['a', 'b', 'c']);
        $loop = $factory->getLastLoop();

        $this->assertSame(0, $loop->iteration);
        $this->assertSame(0, $loop->index);
        $this->assertSame(3, $loop->count);
        $this->assertSame(3, $loop->remaining);
        $this->assertTrue($loop->first);
        $this->assertFalse($loop->last);
        $this->assertFalse($loop->odd);
        $this->assertTrue($loop->even);
        $this->assertSame(1, $loop->depth);
        $this->assertNull($loop->parent);
    }

    public function testAddLoopWithUncountableDataHasNullCountAndRemaining() {
        $factory = $this->factory();

        $factory->addLoop((function () {
            yield 1;
        })());
        $loop = $factory->getLastLoop();

        $this->assertNull($loop->count);
        $this->assertNull($loop->remaining);
        $this->assertNull($loop->last);
    }

    public function testIncrementLoopIndicesAdvancesIterationAndFlipsOddEven() {
        // Every field in the new frame is computed from the *pre-increment*
        // state, so "first"/"index" reflect the iteration that just
        // finished, not the one about to start.
        $factory = $this->factory();
        $factory->addLoop(['a', 'b', 'c']);

        $factory->incrementLoopIndices();
        $loop = $factory->getLastLoop();

        $this->assertSame(1, $loop->iteration);
        $this->assertSame(0, $loop->index);
        $this->assertTrue($loop->first);
        $this->assertTrue($loop->odd);
        $this->assertFalse($loop->even);
        $this->assertSame(2, $loop->remaining);
        $this->assertFalse($loop->last);
    }

    public function testIncrementLoopIndicesDetectsTheLastIteration() {
        $factory = $this->factory();
        $factory->addLoop(['a', 'b']);

        $factory->incrementLoopIndices();
        $factory->incrementLoopIndices();

        $this->assertTrue($factory->getLastLoop()->last);
        $this->assertSame(0, $factory->getLastLoop()->remaining);
    }

    public function testNestedLoopsTrackDepthAndParent() {
        $factory = $this->factory();
        $factory->addLoop(['a']);
        $factory->addLoop(['x', 'y']);

        $outer = $factory->getLoopStack()[0];
        $inner = $factory->getLastLoop();

        $this->assertSame(2, $inner->depth);
        $this->assertSame($outer['iteration'], $inner->parent->iteration);
        $this->assertSame($outer['count'], $inner->parent->count);
    }

    public function testPopLoopRemovesTheInnermostLoop() {
        $factory = $this->factory();
        $factory->addLoop(['a']);
        $factory->addLoop(['x', 'y']);

        $factory->popLoop();

        $this->assertCount(1, $factory->getLoopStack());
        $this->assertSame(1, $factory->getLastLoop()->count);
    }

    public function testGetLastLoopReturnsNullWhenStackIsEmpty() {
        $factory = $this->factory();

        $this->assertNull($factory->getLastLoop());
    }
}
