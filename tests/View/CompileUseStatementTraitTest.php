<?php
use PHPUnit\Framework\TestCase;

class CompileUseStatementTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testUseDirectiveIsCompiled() {
        $this->assertSame(
            '<?php use \App\Foo\Bar; ?>',
            $this->compiler()->compileString('@use(\'App\Foo\Bar\')')
        );
    }

    public function testUseDirectiveWithLeadingBackslashStripsIt() {
        $this->assertSame(
            '<?php use \App\Foo\Bar; ?>',
            $this->compiler()->compileString('@use(\'\App\Foo\Bar\')')
        );
    }

    public function testUseDirectiveWithAliasIsCompiled() {
        $this->assertSame(
            '<?php use \App\Foo\Bar as Baz; ?>',
            $this->compiler()->compileString('@use(\'App\Foo\Bar\', \'Baz\')')
        );
    }
}
