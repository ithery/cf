<?php
use PHPUnit\Framework\TestCase;

class ManageFragmentTraitTest extends TestCase {
    protected function factory() {
        return new CView_Factory();
    }

    public function testStartFragmentAndStopFragmentCapturesBufferedContent() {
        $factory = $this->factory();

        $factory->startFragment('scripts');
        echo 'captured';
        $result = $factory->stopFragment();

        $this->assertSame('captured', $result);
        $this->assertSame('captured', $factory->getFragment('scripts'));
    }

    public function testStopFragmentThrowsWhenNothingWasStarted() {
        $factory = $this->factory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot end a fragment without first starting one.');

        $factory->stopFragment();
    }

    public function testGetFragmentReturnsDefaultWhenMissing() {
        $factory = $this->factory();

        $this->assertNull($factory->getFragment('missing'));
        $this->assertSame('fallback', $factory->getFragment('missing', 'fallback'));
    }

    public function testGetFragmentsReturnsTheWholeArray() {
        $factory = $this->factory();

        $factory->startFragment('a');
        echo '1';
        $factory->stopFragment();

        $factory->startFragment('b');
        echo '2';
        $factory->stopFragment();

        $this->assertSame(['a' => '1', 'b' => '2'], $factory->getFragments());
    }

    public function testNestedFragmentsArePoppedInLifoOrder() {
        $factory = $this->factory();

        $factory->startFragment('outer');
        echo 'before-';
        $factory->startFragment('inner');
        echo 'inner-content';
        $factory->stopFragment();
        echo 'after';
        $factory->stopFragment();

        $this->assertSame('inner-content', $factory->getFragment('inner'));
        $this->assertSame('before-after', $factory->getFragment('outer'));
    }

    public function testFlushFragmentsClearsCapturedFragmentsAndTheActiveStack() {
        $factory = $this->factory();

        $factory->startFragment('a');
        echo '1';
        $factory->stopFragment();

        $factory->flushFragments();

        $this->assertSame([], $factory->getFragments());
    }
}
