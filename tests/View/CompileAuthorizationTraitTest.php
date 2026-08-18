<?php
use PHPUnit\Framework\TestCase;

class CompileAuthorizationTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testCanDirectiveIsCompiled() {
        $this->assertSame(
            "<?php if (c::gate()->check('update', \$post)): ?>",
            $this->compiler()->compileString("@can('update', \$post)")
        );
    }

    public function testCannotDirectiveIsCompiled() {
        $this->assertSame(
            "<?php if (c::gate()->denies('update', \$post)): ?>",
            $this->compiler()->compileString("@cannot('update', \$post)")
        );
    }

    public function testCanAnyDirectiveIsCompiled() {
        $this->assertSame(
            "<?php if (c::gate()->any(['update', 'delete'], \$post)): ?>",
            $this->compiler()->compileString("@canany(['update', 'delete'], \$post)")
        );
    }

    public function testElseCanDirectiveIsCompiled() {
        $this->assertSame(
            "<?php elseif (c::gate()->check('update', \$post)): ?>",
            $this->compiler()->compileString("@elsecan('update', \$post)")
        );
    }

    public function testElseCannotDirectiveIsCompiled() {
        $this->assertSame(
            "<?php elseif (c::gate()->denies('update', \$post)): ?>",
            $this->compiler()->compileString("@elsecannot('update', \$post)")
        );
    }

    public function testElseCanAnyDirectiveIsCompiled() {
        $this->assertSame(
            "<?php elseif (c::gate()->any(['update', 'delete'], \$post)): ?>",
            $this->compiler()->compileString("@elsecanany(['update', 'delete'], \$post)")
        );
    }

    public function testEndCanDirectivesAreCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endcan'));
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endcannot'));
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endcanany'));
    }
}
