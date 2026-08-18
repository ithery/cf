<?php
use PHPUnit\Framework\TestCase;

class CompileRawPhpTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testPhpDirectiveWithExpressionIsCompiled() {
        // compilePhp() doesn't strip parentheses - the matched "($x = 1)"
        // (parens included) is inlined as-is, which is still valid PHP.
        $this->assertSame(
            '<?php ($x = 1); ?>',
            $this->compiler()->compileString('@php($x = 1)')
        );
    }

    public function testPhpDirectiveWithoutExpressionIsLeftAsIs() {
        $this->assertSame(
            '@php',
            $this->compiler()->compileString('@php')
        );
    }

    public function testPhpBlockIsWrappedInPhpTags() {
        $result = $this->compiler()->compileString("@php\n\$x = 1;\n@endphp");

        $this->assertSame("<?php\n\$x = 1;\n?>", $result);
    }

    public function testUnsetDirectiveIsCompiled() {
        $this->assertSame(
            '<?php unset($x); ?>',
            $this->compiler()->compileString('@unset($x)')
        );
    }
}
