<?php
use PHPUnit\Framework\TestCase;

class CompileLoopTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testForeachDirectiveIsCompiled() {
        $expected = '<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); '
            . 'foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>';

        $this->assertSame($expected, $this->compiler()->compileString('@foreach ($users as $user)'));
    }

    public function testForeachDirectiveWithKeyValueIsCompiled() {
        $expected = '<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); '
            . 'foreach($__currentLoopData as $id => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>';

        $this->assertSame($expected, $this->compiler()->compileString('@foreach ($users as $id => $user)'));
    }

    public function testEndForeachDirectiveIsCompiled() {
        $this->assertSame(
            '<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>',
            $this->compiler()->compileString('@endforeach')
        );
    }

    public function testForelseDirectiveIsCompiled() {
        $expected = '<?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); '
            . 'foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>';

        $this->assertSame($expected, $this->compiler()->compileString('@forelse ($users as $user)'));
    }

    public function testEmptyDirectiveWithoutArgumentsClosesForelseLoop() {
        $compiler = $this->compiler();
        // forElseCounter is incremented as a side effect of compiling @forelse,
        // and @empty (no-args form) consumes/decrements it to match up.
        $compiler->compileString('@forelse ($users as $user)');

        $this->assertSame(
            '<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>',
            $compiler->compileString('@empty')
        );
    }

    public function testEmptyDirectiveWithArgumentsIsAPlainEmptyCheck() {
        $this->assertSame(
            '<?php if(empty($users)): ?>',
            $this->compiler()->compileString('@empty($users)')
        );
    }

    public function testEndforelseDirectiveIsCompiled() {
        $this->assertSame('<?php endif; ?>', $this->compiler()->compileString('@endforelse'));
    }

    public function testForDirectiveIsCompiled() {
        $this->assertSame(
            '<?php for($i = 0; $i < 10; $i++): ?>',
            $this->compiler()->compileString('@for ($i = 0; $i < 10; $i++)')
        );
    }

    public function testEndForDirectiveIsCompiled() {
        $this->assertSame('<?php endfor; ?>', $this->compiler()->compileString('@endfor'));
    }

    public function testWhileDirectiveIsCompiled() {
        $this->assertSame(
            '<?php while($x < 10): ?>',
            $this->compiler()->compileString('@while ($x < 10)')
        );
    }

    public function testEndWhileDirectiveIsCompiled() {
        $this->assertSame('<?php endwhile; ?>', $this->compiler()->compileString('@endwhile'));
    }

    public function testBreakDirectiveWithoutArgumentsIsCompiled() {
        $this->assertSame('<?php break; ?>', $this->compiler()->compileString('@break'));
    }

    public function testBreakDirectiveWithNumericLevelIsCompiled() {
        $this->assertSame('<?php break 2; ?>', $this->compiler()->compileString('@break(2)'));
    }

    public function testBreakDirectiveWithConditionIsCompiled() {
        $this->assertSame(
            '<?php if($count > 10) break; ?>',
            $this->compiler()->compileString('@break($count > 10)')
        );
    }

    public function testContinueDirectiveWithoutArgumentsIsCompiled() {
        $this->assertSame('<?php continue; ?>', $this->compiler()->compileString('@continue'));
    }

    public function testContinueDirectiveWithNumericLevelIsCompiled() {
        $this->assertSame('<?php continue 2; ?>', $this->compiler()->compileString('@continue(2)'));
    }

    public function testContinueDirectiveWithConditionIsCompiled() {
        $this->assertSame(
            '<?php if($user->type == "admin") continue; ?>',
            $this->compiler()->compileString('@continue($user->type == "admin")')
        );
    }
}
