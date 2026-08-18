<?php
use PHPUnit\Framework\TestCase;

class CompileCommentTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testCommentsAreStrippedEntirely() {
        $this->assertSame(
            'before  after',
            $this->compiler()->compileString('before {{-- a comment --}} after')
        );
    }

    public function testMultilineCommentsAreStripped() {
        $this->assertSame(
            'before  after',
            $this->compiler()->compileString("before {{--\nspans\nmultiple\nlines\n--}} after")
        );
    }

    public function testMultipleCommentsInTheSameStringAreAllStripped() {
        $this->assertSame(
            'a  b  c',
            $this->compiler()->compileString('a {{-- one --}} b {{-- two --}} c')
        );
    }
}
