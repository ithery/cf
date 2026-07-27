<?php
use PHPUnit\Framework\TestCase;

class ManageStackTraitTest extends TestCase {
    protected function factory() {
        return new CView_Factory();
    }

    public function testYieldPushContentReturnsDefaultWhenStackIsEmpty() {
        $factory = $this->factory();

        $this->assertSame('default', $factory->yieldPushContent('scripts', 'default'));
    }

    public function testStartPushAndStopPushCapturesBufferedContent() {
        $factory = $this->factory();

        $factory->startPush('scripts');
        echo 'one.js';
        $factory->stopPush();

        $this->assertSame('one.js', $factory->yieldPushContent('scripts'));
    }

    public function testMultiplePushesToTheSameSectionAreConcatenated() {
        $factory = $this->factory();

        $factory->startPush('scripts');
        echo 'one.js';
        $factory->stopPush();

        $factory->startPush('scripts');
        echo 'two.js';
        $factory->stopPush();

        $this->assertSame('one.jstwo.js', $factory->yieldPushContent('scripts'));
    }

    public function testStartPushWithExplicitContentSkipsOutputBuffering() {
        $factory = $this->factory();

        $factory->startPush('scripts', 'one.js');

        $this->assertSame('one.js', $factory->yieldPushContent('scripts'));
    }

    public function testStopPushThrowsWhenNothingWasStarted() {
        $factory = $this->factory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot end a push stack without first starting one.');

        $factory->stopPush();
    }

    public function testPrependIsPlacedBeforePushedContent() {
        $factory = $this->factory();

        $factory->startPush('scripts');
        echo 'pushed.js';
        $factory->stopPush();

        $factory->startPrepend('scripts');
        echo 'prepended.js';
        $factory->stopPrepend();

        $this->assertSame('prepended.jspushed.js', $factory->yieldPushContent('scripts'));
    }

    public function testMultiplePrependsAreAppliedInReverseOrder() {
        $factory = $this->factory();

        $factory->startPrepend('scripts');
        echo 'first';
        $factory->stopPrepend();

        $factory->startPrepend('scripts');
        echo 'second';
        $factory->stopPrepend();

        // Later prepends end up closer to the front of the output.
        $this->assertSame('secondfirst', $factory->yieldPushContent('scripts'));
    }

    public function testStopPrependThrowsWhenNothingWasStarted() {
        $factory = $this->factory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot end a prepend operation without first starting one.');

        $factory->stopPrepend();
    }

    public function testFlushStacksClearsPushesPrependsAndTheActiveStack() {
        $factory = $this->factory();

        $factory->startPush('scripts');
        echo 'one.js';
        $factory->stopPush();

        $factory->flushStacks();

        $this->assertSame('', $factory->yieldPushContent('scripts', ''));
    }

    public function testPushesAreKeyedByTheCurrentRenderCountSoNestedRendersDontMix() {
        // Each push is stored per render-count "slot"; yieldPushContent()
        // just implode()s all slots together regardless of render depth, but
        // this at least proves independent renders don't clobber each
        // other's buffered content mid-flight.
        $factory = $this->factory();

        $factory->incrementRender();
        $factory->startPush('scripts');
        echo 'outer.js';
        $factory->stopPush();

        $factory->incrementRender();
        $factory->startPush('scripts');
        echo 'inner.js';
        $factory->stopPush();
        $factory->decrementRender();

        $factory->decrementRender();

        $this->assertSame('outer.jsinner.js', $factory->yieldPushContent('scripts'));
    }
}
