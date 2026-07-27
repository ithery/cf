<?php
use PHPUnit\Framework\TestCase;

class CompileHelperTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testCsrfDirectiveIsCompiled() {
        $this->assertSame(
            '<?php echo c::csrfField(); ?>',
            $this->compiler()->compileString('@csrf')
        );
    }

    public function testMethodDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo c::methodField('PUT'); ?>",
            $this->compiler()->compileString("@method('PUT')")
        );
    }

    public function testDdDirectiveIsCompiled() {
        $this->assertSame(
            '<?php cdbg::dd($value); ?>',
            $this->compiler()->compileString('@dd($value)')
        );
    }

    public function testDumpDirectiveIsCompiled() {
        $this->assertSame(
            '<?php cdbg::d($value); ?>',
            $this->compiler()->compileString('@dump($value)')
        );
    }

    public function testViteDirectiveWithoutArgumentsDefaultsToEmptyCall() {
        $this->assertSame(
            "<?php echo c::container('CBase_Vite')(); ?>",
            $this->compiler()->compileString('@vite')
        );
    }

    public function testViteDirectiveWithArguments() {
        $this->assertSame(
            "<?php echo c::container('CBase_Vite')(['resources/js/app.js']); ?>",
            $this->compiler()->compileString("@vite(['resources/js/app.js'])")
        );
    }

    public function testViteReactRefreshDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo c::container('CBase_Vite')->reactRefresh(); ?>",
            $this->compiler()->compileString('@viteReactRefresh')
        );
    }
}
