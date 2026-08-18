<?php
use PHPUnit\Framework\TestCase;

class CompileFragmentTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testFragmentDirectiveIsCompiled() {
        $this->assertSame(
            "<?php \$__env->startFragment('scripts'); ?>",
            $this->compiler()->compileString("@fragment('scripts')")
        );
    }

    public function testEndFragmentDirectiveIsCompiled() {
        $this->assertSame(
            '<?php echo $__env->stopFragment(); ?>',
            $this->compiler()->compileString('@endfragment')
        );
    }
}
