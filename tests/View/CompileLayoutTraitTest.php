<?php
use PHPUnit\Framework\TestCase;

class CompileLayoutTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testExtendsDirectiveIsMovedToTheFooter() {
        // @extends compiles to an empty string in place, and defers the
        // actual echo to the end of the template via the compiler's footer.
        // addFooters() also ltrim()s the leading newline left behind by the
        // now-empty @extends(...) line.
        $result = $this->compiler()->compileString("@extends('layouts.app')\ncontent");

        $this->assertSame(
            "content\n<?php echo \$__env->make('layouts.app', carr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>",
            $result
        );
    }

    public function testExtendsFirstDirectiveIsMovedToTheFooter() {
        $result = $this->compiler()->compileString("@extendsFirst(['layouts.custom', 'layouts.app'])\ncontent");

        $this->assertSame(
            "content\n<?php echo \$__env->first(['layouts.custom', 'layouts.app'], carr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>",
            $result
        );
    }

    public function testSectionDirectiveIsCompiled() {
        $this->assertSame(
            "<?php \$__env->startSection('content'); ?>",
            $this->compiler()->compileString("@section('content')")
        );
    }

    public function testYieldDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo \$__env->yieldContent('content'); ?>",
            $this->compiler()->compileString("@yield('content')")
        );
    }

    public function testYieldDirectiveWithDefaultIsCompiled() {
        $this->assertSame(
            "<?php echo \$__env->yieldContent('title', 'Default Title'); ?>",
            $this->compiler()->compileString("@yield('title', 'Default Title')")
        );
    }

    public function testShowDirectiveIsCompiled() {
        $this->assertSame(
            '<?php echo $__env->yieldSection(); ?>',
            $this->compiler()->compileString('@show')
        );
    }

    public function testAppendDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $__env->appendSection(); ?>',
            $this->compiler()->compileString('@append')
        );
    }

    public function testOverwriteDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $__env->stopSection(true); ?>',
            $this->compiler()->compileString('@overwrite')
        );
    }

    public function testStopDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $__env->stopSection(); ?>',
            $this->compiler()->compileString('@stop')
        );
    }

    public function testEndSectionDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $__env->stopSection(); ?>',
            $this->compiler()->compileString('@endsection')
        );
    }
}
