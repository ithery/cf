<?php
use PHPUnit\Framework\TestCase;

class CompileTranslationTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testLangDirectiveWithoutArgumentsStartsTranslationBlock() {
        $this->assertSame(
            '<?php $__env->startTranslation(); ?>',
            $this->compiler()->compileString('@lang')
        );
    }

    public function testLangDirectiveWithReplacementArrayStartsTranslationBlock() {
        $this->assertSame(
            "<?php \$__env->startTranslation(['name' => 'John']); ?>",
            $this->compiler()->compileString("@lang(['name' => 'John'])")
        );
    }

    public function testLangDirectiveWithKeyEchoesTranslation() {
        $this->assertSame(
            "<?php echo c::trans()->get('messages.welcome'); ?>",
            $this->compiler()->compileString("@lang('messages.welcome')")
        );
    }

    public function testEndLangDirectiveRendersTranslation() {
        $this->assertSame(
            '<?php echo $__env->renderTranslation(); ?>',
            $this->compiler()->compileString('@endlang')
        );
    }

    public function testChoiceDirectiveIsCompiled() {
        $this->assertSame(
            "<?php echo c::trans()->choice('messages.apples', 10); ?>",
            $this->compiler()->compileString("@choice('messages.apples', 10)")
        );
    }
}
