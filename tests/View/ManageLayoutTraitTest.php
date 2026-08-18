<?php
use PHPUnit\Framework\TestCase;

class ManageLayoutTraitTest extends TestCase {
    protected function factory() {
        return new CView_Factory();
    }

    public function testYieldContentReturnsDefaultWhenSectionIsMissing() {
        $factory = $this->factory();

        $this->assertSame('default', $factory->yieldContent('content', 'default'));
    }

    public function testStartSectionAndStopSectionCapturesBufferedContent() {
        $factory = $this->factory();

        $factory->startSection('content');
        echo 'body';
        $factory->stopSection();

        $this->assertSame('body', $factory->yieldContent('content'));
    }

    public function testStartSectionWithInlineContentSkipsOutputBuffering() {
        $factory = $this->factory();

        $factory->startSection('content', 'inline');

        $this->assertSame('inline', $factory->yieldContent('content'));
    }

    public function testInjectIsAnAliasForStartSectionWithInlineContent() {
        $factory = $this->factory();

        $factory->inject('content', 'injected');

        $this->assertSame('injected', $factory->yieldContent('content'));
    }

    public function testStopSectionThrowsWhenNothingWasStarted() {
        $factory = $this->factory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot end a section without first starting one.');

        $factory->stopSection();
    }

    public function testRestartingASectionWithoutAtParentKeepsTheOriginalContent() {
        // extendSection() only *replaces* an existing "@parent" placeholder
        // token with the new content - without that token present, there's
        // nothing to substitute, so the new content is silently discarded
        // and the original section content wins. Real @parent-based
        // inheritance is covered by testExtendingASectionSubstitutesTheParentPlaceholder.
        $factory = $this->factory();

        $factory->startSection('content');
        echo 'first';
        $factory->stopSection();

        $factory->startSection('content');
        echo 'second';
        $factory->stopSection();

        $this->assertSame('first', $factory->yieldContent('content'));
    }

    public function testStopSectionWithOverwriteReplacesThePreviousContent() {
        $factory = $this->factory();

        $factory->startSection('content');
        echo 'first';
        $factory->stopSection();

        $factory->startSection('content');
        echo 'second';
        $factory->stopSection($overwrite = true);

        $this->assertSame('second', $factory->yieldContent('content'));
    }

    public function testYieldSectionStopsTheActiveSectionAndYieldsIt() {
        $factory = $this->factory();

        $factory->startSection('content');
        echo 'body';
        $result = $factory->yieldSection();

        $this->assertSame('body', $result);
    }

    public function testYieldSectionReturnsEmptyStringWhenNothingIsActive() {
        $factory = $this->factory();

        $this->assertSame('', $factory->yieldSection());
    }

    public function testAppendSectionAddsToThePreviouslyStoppedSection() {
        $factory = $this->factory();

        $factory->startSection('content');
        echo 'first';
        $factory->stopSection();

        $factory->startSection('content');
        echo 'second';
        $factory->appendSection();

        $this->assertSame('firstsecond', $factory->yieldContent('content'));
    }

    public function testAppendSectionThrowsWhenNothingWasStarted() {
        $factory = $this->factory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot end a section without first starting one.');

        $factory->appendSection();
    }

    public function testAtParentPlaceholderIsRemovedFromTheOutermostSection() {
        $factory = $this->factory();

        $factory->startSection('content');
        echo 'before-' . CView_Factory::parentPlaceholder('content') . '-after';
        $factory->stopSection();

        $this->assertSame('before--after', $factory->yieldContent('content'));
    }

    public function testExtendingASectionSubstitutesTheParentPlaceholder() {
        $factory = $this->factory();

        $factory->startSection('content');
        echo 'child-' . CView_Factory::parentPlaceholder('content');
        $factory->stopSection();

        $factory->startSection('content');
        echo 'parent';
        $factory->stopSection();

        $this->assertSame('child-parent', $factory->yieldContent('content'));
    }

    public function testDoubleAtParentIsUnescapedToALiteralAtParentToken() {
        // "@@parent" is Blade's own escape for a literal "@parent" that
        // should NOT be treated as the section-inheritance placeholder.
        $factory = $this->factory();

        $factory->startSection('content', '@@parent');

        $this->assertSame('@parent', $factory->yieldContent('content'));
    }

    public function testHasSectionAndSectionMissing() {
        $factory = $this->factory();
        $factory->startSection('content', 'body');

        $this->assertTrue($factory->hasSection('content'));
        $this->assertFalse($factory->hasSection('missing'));
        $this->assertTrue($factory->sectionMissing('missing'));
        $this->assertFalse($factory->sectionMissing('content'));
    }

    public function testGetSectionAndGetSectionsReturnRawUncompiledContent() {
        $factory = $this->factory();
        $factory->startSection('content', 'body');

        $this->assertSame('body', $factory->getSection('content'));
        $this->assertNull($factory->getSection('missing'));
        $this->assertSame('fallback', $factory->getSection('missing', 'fallback'));
        $this->assertSame(['content' => 'body'], $factory->getSections());
    }

    public function testFlushSectionsClearsSectionsAndTheActiveStack() {
        $factory = $this->factory();
        $factory->startSection('content', 'body');

        $factory->flushSections();

        $this->assertSame([], $factory->getSections());
    }
}
