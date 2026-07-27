<?php
use PHPUnit\Framework\TestCase;

class CompileConditionalTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testIfDirectiveIsCompiled() {
        $this->assertSame('<?php if(true): ?>', $this->compiler()->compileString('@if(true)'));
    }

    public function testElseifDirectiveIsCompiled() {
        $this->assertSame('<?php elseif(false): ?>', $this->compiler()->compileString('@elseif(false)'));
    }

    public function testElseDirectiveIsCompiled() {
        $this->assertSame('<?php else: ?>', $this->compiler()->compileString('@else'));
    }

    public function testEndifDirectiveIsCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endif'));
    }

    public function testUnlessDirectiveNegatesTheCondition() {
        $this->assertSame(
            '<?php if (! ($record->isEmpty())): ?>',
            $this->compiler()->compileString('@unless ($record->isEmpty())')
        );
    }

    public function testEndunlessDirectiveIsCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endunless'));
    }

    public function testIssetDirectiveIsCompiled() {
        $this->assertSame(
            '<?php if(isset($name)): ?>',
            $this->compiler()->compileString('@isset($name)')
        );
    }

    public function testEndIssetDirectiveIsCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endisset'));
    }

    public function testAuthDirectiveWithoutGuardIsCompiled() {
        $this->assertSame(
            '<?php if(c::auth()->guard()->check()): ?>',
            $this->compiler()->compileString('@auth')
        );
    }

    public function testAuthDirectiveWithGuardIsCompiled() {
        $this->assertSame(
            "<?php if(c::auth()->guard('admin')->check()): ?>",
            $this->compiler()->compileString("@auth('admin')")
        );
    }

    public function testElseAuthDirectiveIsCompiled() {
        $this->assertSame(
            '<?php elseif(c::auth()->guard()->check()): ?>',
            $this->compiler()->compileString('@elseauth')
        );
    }

    public function testEndAuthDirectiveIsCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endauth'));
    }

    public function testGuestDirectiveWithoutGuardIsCompiled() {
        $this->assertSame(
            '<?php if(c::auth()->guard()->guest()): ?>',
            $this->compiler()->compileString('@guest')
        );
    }

    public function testGuestDirectiveWithGuardIsCompiled() {
        $this->assertSame(
            "<?php if(c::auth()->guard('admin')->guest()): ?>",
            $this->compiler()->compileString("@guest('admin')")
        );
    }

    public function testElseGuestDirectiveIsCompiled() {
        $this->assertSame(
            '<?php elseif(c::auth()->guard()->guest()): ?>',
            $this->compiler()->compileString('@elseguest')
        );
    }

    public function testEndGuestDirectiveIsCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endguest'));
    }

    public function testEnvDirectiveIsCompiled() {
        $this->assertSame(
            "<?php if(c::environment('production')): ?>",
            $this->compiler()->compileString("@env('production')")
        );
    }

    public function testEndEnvDirectiveIsCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endenv'));
    }

    public function testProductionDirectiveIsCompiled() {
        $this->assertSame(
            '<?php if(CF::isProduction()): ?>',
            $this->compiler()->compileString('@production')
        );
    }

    public function testEndProductionDirectiveIsCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endproduction'));
    }

    public function testHasSectionDirectiveIsCompiled() {
        $this->assertSame(
            "<?php if (! empty(trim(\$__env->yieldContent('content')))): ?>",
            $this->compiler()->compileString("@hasSection('content')")
        );
    }

    public function testSectionMissingDirectiveIsCompiled() {
        $this->assertSame(
            "<?php if (empty(trim(\$__env->yieldContent('content')))): ?>",
            $this->compiler()->compileString("@sectionMissing('content')")
        );
    }

    public function testSwitchCaseDefaultAndEndSwitchAreCompiled() {
        // compileCase() doesn't strip parentheses off its expression - the
        // matched "('one')" (parens included) is inlined as-is.
        $compiler = $this->compiler();

        $this->assertSame('<?php switch($type):', $compiler->compileString('@switch($type)'));
        // The first @case in a switch omits the leading "<?php" since it
        // follows immediately after the "@switch(...):" opening tag.
        $this->assertSame("case ('one'): ?>", $compiler->compileString("@case('one')"));
        $this->assertSame("<?php case ('two'): ?>", $compiler->compileString("@case('two')"));
        $this->assertSame('<?php default: ?>', $compiler->compileString('@default'));
        $this->assertSame('<?php endswitch; ?>', $compiler->compileString('@endswitch'));
    }

    public function testSwitchResetsFirstCaseFlagOnEachNewSwitch() {
        $compiler = $this->compiler();

        $compiler->compileString('@switch($a)');
        $compiler->compileString("@case('x')");
        $compiler->compileString('@endswitch');

        $compiler->compileString('@switch($b)');
        $this->assertSame("case ('y'): ?>", $compiler->compileString("@case('y')"));
    }

    public function testOnceDirectiveWithoutIdGeneratesAUuid() {
        $result = $this->compiler()->compileString('@once');

        $this->assertMatchesRegularExpression(
            "/^<\?php if \(! \\\$__env->hasRenderedOnce\('[0-9a-f-]{36}'\)\): \\\$__env->markAsRenderedOnce\('[0-9a-f-]{36}'\); \?>$/",
            $result
        );
    }

    public function testOnceDirectiveWithExplicitIdIsCompiled() {
        $this->assertSame(
            "<?php if (! \$__env->hasRenderedOnce('my-id')): \$__env->markAsRenderedOnce('my-id'); ?>",
            $this->compiler()->compileString("@once('my-id')")
        );
    }

    public function testEndOnceDirectiveIsCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endonce'));
    }

    public function testSelectedDirectiveIsCompiled() {
        $this->assertSame(
            "<?php if(\$value == 'active'): echo 'selected'; endif; ?>",
            $this->compiler()->compileString("@selected(\$value == 'active')")
        );
    }

    public function testCheckedDirectiveIsCompiled() {
        $this->assertSame(
            '<?php if($isChecked): echo \'checked\'; endif; ?>',
            $this->compiler()->compileString('@checked($isChecked)')
        );
    }

    public function testDisabledDirectiveIsCompiled() {
        $this->assertSame(
            '<?php if($isDisabled): echo \'disabled\'; endif; ?>',
            $this->compiler()->compileString('@disabled($isDisabled)')
        );
    }
}
