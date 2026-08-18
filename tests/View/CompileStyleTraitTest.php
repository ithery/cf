<?php
use PHPUnit\Framework\TestCase;

class CompileStyleTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testStyleDirectiveIsCompiled() {
        $this->assertSame(
            "style=\"<?php echo \carr::toCssStyles(['color: red' => true]) ?>\"",
            $this->compiler()->compileString("@style(['color: red' => true])")
        );
    }

    public function testStyleDirectiveWithoutArgumentsDefaultsToEmptyArray() {
        $this->assertSame(
            "style=\"<?php echo \carr::toCssStyles([]) ?>\"",
            $this->compiler()->compileString('@style')
        );
    }
}
