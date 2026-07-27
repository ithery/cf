<?php
use PHPUnit\Framework\TestCase;

class CompileSessionTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testSessionDirectiveIsCompiled() {
        $expected = '<?php $__sessionArgs = [\'status\'];
if (session()->has($__sessionArgs[0])) :
if (isset($value)) { $__sessionPrevious[] = $value; }
$value = session()->get($__sessionArgs[0]); ?>';

        $this->assertSame($expected, $this->compiler()->compileString("@session('status')"));
    }

    public function testEndSessionDirectiveIsCompiled() {
        $expected = '<?php unset($value);
if (isset($__sessionPrevious) && !empty($__sessionPrevious)) { $value = array_pop($__sessionPrevious); }
if (isset($__sessionPrevious) && empty($__sessionPrevious)) { unset($__sessionPrevious); }
endif;
unset($__sessionArgs); ?>';

        $this->assertSame($expected, $this->compiler()->compileString('@endsession'));
    }
}
