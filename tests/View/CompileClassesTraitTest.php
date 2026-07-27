<?php
use PHPUnit\Framework\TestCase;

class CompileClassesTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testClassDirectiveIsCompiled() {
        $this->assertSame(
            "class=\"<?php echo \carr::toCssClasses(['class-a', 'class-b' => true]) ?>\"",
            $this->compiler()->compileString("@class(['class-a', 'class-b' => true])")
        );
    }

    public function testClassDirectiveWithoutArgumentsDefaultsToEmptyArray() {
        $this->assertSame(
            "class=\"<?php echo \carr::toCssClasses([]) ?>\"",
            $this->compiler()->compileString('@class')
        );
    }
}
