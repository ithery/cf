<?php
use PHPUnit\Framework\TestCase;

class CompileStackTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testStackDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo \$__env->yieldPushContent('scripts'); ?>",
            $this->compiler()->compileString("@stack('scripts')")
        );
    }

    public function testPushDirectiveIsCompiled() {
        $this->assertSame(
            "<?php \$__env->startPush('scripts'); ?>",
            $this->compiler()->compileString("@push('scripts')")
        );
    }

    public function testEndPushDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $__env->stopPush(); ?>',
            $this->compiler()->compileString('@endpush')
        );
    }

    public function testPrependDirectiveIsCompiled() {
        $this->assertSame(
            "<?php \$__env->startPrepend('scripts'); ?>",
            $this->compiler()->compileString("@prepend('scripts')")
        );
    }

    public function testEndPrependDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $__env->stopPrepend(); ?>',
            $this->compiler()->compileString('@endprepend')
        );
    }

    public function testPushOnceDirectiveWithExplicitIdIsCompiled() {
        $expected = "<?php if (! \$__env->hasRenderedOnce('once-id')): \$__env->markAsRenderedOnce('once-id');\n\$__env->startPush('scripts'); ?>";

        $this->assertSame($expected, $this->compiler()->compileString("@pushOnce('scripts', 'once-id')"));
    }

    public function testEndPushOnceDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $__env->stopPush(); endif; ?>',
            $this->compiler()->compileString('@endPushOnce')
        );
    }

    public function testPrependOnceDirectiveWithExplicitIdIsCompiled() {
        $expected = "<?php if (! \$__env->hasRenderedOnce('once-id')): \$__env->markAsRenderedOnce('once-id');\n\$__env->startPrepend('scripts'); ?>";

        $this->assertSame($expected, $this->compiler()->compileString("@prependOnce('scripts', 'once-id')"));
    }

    public function testEndPrependOnceDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $__env->stopPrepend(); endif; ?>',
            $this->compiler()->compileString('@endprependOnce')
        );
    }
}
