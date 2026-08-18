<?php
use PHPUnit\Framework\TestCase;

class CompileIncludeTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testEachDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo \$__env->renderEach('view.name', \$items); ?>",
            $this->compiler()->compileString("@each('view.name', \$items)")
        );
    }

    public function testIncludeDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo \$__env->make('view.name', \carr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>",
            $this->compiler()->compileString("@include('view.name')")
        );
    }

    public function testIncludeIfDirectiveIsCompiled() {
        $this->assertSame(
            "<?php if (\$__env->exists('view.name')) echo \$__env->make('view.name', \carr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>",
            $this->compiler()->compileString("@includeIf('view.name')")
        );
    }

    public function testIncludeWhenDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo \$__env->renderWhen(\$condition, 'view.name', \carr::except(get_defined_vars(), ['__data', '__path'])); ?>",
            $this->compiler()->compileString("@includeWhen(\$condition, 'view.name')")
        );
    }

    public function testIncludeUnlessDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo \$__env->renderWhen(! \$condition, 'view.name', \carr::except(get_defined_vars(), ['__data', '__path'])); ?>",
            $this->compiler()->compileString("@includeUnless(\$condition, 'view.name')")
        );
    }

    public function testIncludeFirstDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo \$__env->first(['view.name', 'view.fallback'], \carr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>",
            $this->compiler()->compileString("@includeFirst(['view.name', 'view.fallback'])")
        );
    }
}
