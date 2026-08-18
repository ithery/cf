<?php
use PHPUnit\Framework\TestCase;

class CompileJsTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testJsDirectiveIsCompiled() {
        $this->assertSame(
            '<?php echo \CBase_Js::from($users)->toHtml() ?>',
            $this->compiler()->compileString('@js($users)')
        );
    }
}
