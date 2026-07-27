<?php
use PHPUnit\Framework\TestCase;

class CompileErrorTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testErrorDirectiveIsCompiled() {
        $expected = '<?php $__errorArgs = [\'email\'];
$__bag = $errors->getBag(isset($__errorArgs[1]) ? $__errorArgs[1] : \'default\');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>';

        $this->assertSame($expected, $this->compiler()->compileString("@error('email')"));
    }

    public function testEndErrorDirectiveIsCompiled() {
        $expected = '<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>';

        $this->assertSame($expected, $this->compiler()->compileString('@enderror'));
    }
}
