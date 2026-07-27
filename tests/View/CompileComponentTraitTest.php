<?php
use PHPUnit\Framework\TestCase;

class CompileComponentTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testComponentDirectiveForAViewNameStartsAComponent() {
        $this->assertSame(
            "<?php \$__env->startComponent('alert', ['type' => 'error']); ?>",
            $this->compiler()->compileString("@component('alert', ['type' => 'error'])")
        );
    }

    public function testComponentDirectiveWithOnlyAViewNameStartsAComponent() {
        $this->assertSame(
            "<?php \$__env->startComponent('alert'); ?>",
            $this->compiler()->compileString("@component('alert')")
        );
    }

    public function testComponentDirectiveForAClassResolvesTheComponentClass() {
        // A component name is only routed to the class-component branch when
        // it contains "::class" or a backslash (compileComponent's
        // cstr::contains($component, ['::class', '\\']) check) - a quoted,
        // namespaced class string like this qualifies via the backslash.
        $result = $this->compiler()->compileString('@component(\'App\View\Components\Alert\', \'alert\', [\'type\' => \'error\'])');

        $this->assertStringContainsString(
            '$component = App\View\Components\Alert::resolve([\'type\' => \'error\'] + (isset($attributes) && $attributes instanceof \CView_ComponentAttributeBag ? (array) $attributes->getIterator() : []));',
            $result
        );
        $this->assertStringContainsString('$component->withName(\'alert\');', $result);
        $this->assertStringContainsString('if ($component->shouldRender()):', $result);
        $this->assertStringContainsString('$__env->startComponent($component->resolveView(), $component->data());', $result);
    }

    public function testEndComponentDirectiveRendersTheComponent() {
        $this->assertSame(
            '<?php echo $__env->renderComponent(); ?>',
            $this->compiler()->compileString('@endcomponent')
        );
    }

    public function testEndComponentClassDirectiveClosesOutTheClassComponentScaffolding() {
        $compiler = $this->compiler();
        // compileEndComponentClass() pops a hash pushed by the matching
        // compileComponent() class-component branch, so they must run
        // through the same compiler instance in the same open/close order.
        $compiler->compileString('@component(\'App\View\Components\Alert\', \'alert\', [])');

        $result = $compiler->compileString('@endComponentClass');

        $this->assertStringContainsString('echo $__env->renderComponent();', $result);
        $this->assertStringContainsString('<?php endif; ?>', $result);
        $this->assertStringContainsString('$component = $__componentOriginal', $result);
    }

    public function testSlotDirectiveIsCompiled() {
        $this->assertSame(
            "<?php \$__env->slot('title'); ?>",
            $this->compiler()->compileString("@slot('title')")
        );
    }

    public function testEndSlotDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $__env->endSlot(); ?>',
            $this->compiler()->compileString('@endslot')
        );
    }

    public function testComponentFirstDirectiveIsCompiled() {
        $this->assertSame(
            "<?php \$__env->startComponentFirst(['alert', 'default-alert']); ?>",
            $this->compiler()->compileString("@componentFirst(['alert', 'default-alert'])")
        );
    }

    public function testEndComponentFirstDirectiveRendersTheComponent() {
        $this->assertSame(
            '<?php echo $__env->renderComponent(); ?>',
            $this->compiler()->compileString('@endComponentFirst')
        );
    }

    public function testAwareDirectiveIsCompiled() {
        // compileAware() doesn't strip parentheses either - the matched
        // "(['color'])" (parens included) is inlined as-is.
        $expected = "<?php foreach ((['color']) as \$__key => \$__value) {\n"
            . "    \$__consumeVariable = is_string(\$__key) ? \$__key : \$__value;\n"
            . "    \$\$__consumeVariable = is_string(\$__key) ? \$__env->getConsumableComponentData(\$__key, \$__value) : \$__env->getConsumableComponentData(\$__value);\n"
            . '} ?>';

        $this->assertSame($expected, $this->compiler()->compileString("@aware(['color'])"));
    }

    public function testSanitizeComponentAttributeEscapesPlainStrings() {
        $this->assertSame(
            '&lt;script&gt;',
            CView_Compiler_BladeCompiler::sanitizeComponentAttribute('<script>')
        );
    }

    public function testSanitizeComponentAttributeLeavesNonStringsAlone() {
        $this->assertSame(42, CView_Compiler_BladeCompiler::sanitizeComponentAttribute(42));
        $this->assertTrue(CView_Compiler_BladeCompiler::sanitizeComponentAttribute(true));
    }

    public function testSanitizeComponentAttributeLeavesComponentAttributeBagAlone() {
        $bag = new CView_ComponentAttributeBag(['class' => 'foo']);

        $this->assertSame($bag, CView_Compiler_BladeCompiler::sanitizeComponentAttribute($bag));
    }
}
