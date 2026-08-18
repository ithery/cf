<?php
use PHPUnit\Framework\TestCase;

class CompileEchoTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testRegularEchoIsWrappedInEscapeHelper() {
        $this->assertSame(
            '<?php echo c::e($name); ?>',
            $this->compiler()->compileString('{{ $name }}')
        );
    }

    public function testEscapedEchoUsesTripleCurlyBraces() {
        $this->assertSame(
            '<?php echo c::e($name); ?>',
            $this->compiler()->compileString('{{{ $name }}}')
        );
    }

    public function testRawEchoIsNotEscaped() {
        $this->assertSame(
            '<?php echo $name; ?>',
            $this->compiler()->compileString('{!! $name !!}')
        );
    }

    public function testEscapedRegularEchoTagIsUnescapedButLeftLiteral() {
        // A leading "@" before "{{" escapes Blade's echo handling entirely,
        // leaving the raw "{{ $name }}" text (minus the "@") in the output.
        $this->assertSame(
            '{{ $name }}',
            $this->compiler()->compileString('@{{ $name }}')
        );
    }

    public function testTrailingNewlineAfterEchoIsPreserved() {
        $this->assertSame(
            "<?php echo c::e(\$name); ?>\n\n",
            $this->compiler()->compileString("{{ \$name }}\n")
        );
    }

    public function testWhitespaceInsideDelimitersIsTrimmed() {
        $this->assertSame(
            '<?php echo c::e($name); ?>',
            $this->compiler()->compileString('{{   $name   }}')
        );
    }

    public function testMultipleEchosOnTheSameLine() {
        $this->assertSame(
            '<?php echo c::e($first); ?> and <?php echo c::e($last); ?>',
            $this->compiler()->compileString('{{ $first }} and {{ $last }}')
        );
    }

    public function testStringableHandlerWrapsEscapedTripleBraceEchoes() {
        // The echo-handler wrap only applies to the escaped {{{ }}} tag, not
        // the regular {{ }} one (compileRegularEchos never calls
        // wrapInEchoHandler()) - and once any handler is registered,
        // compileString() also prefixes a $__bladeCompiler bootstrap line.
        $compiler = $this->compiler();
        $compiler->stringable('DateTime', function ($value) {
            return "custom_render({$value})";
        });

        $this->assertSame(
            '<?php $__bladeCompiler = CView::blade(); ?><?php echo c::e($__bladeCompiler->applyEchoHandler($date)); ?>',
            $compiler->compileString('{{{ $date }}}')
        );
    }

    public function testApplyEchoHandlerInvokesRegisteredHandlerForMatchingClass() {
        $compiler = $this->compiler();
        $compiler->stringable(DateTime::class, function (DateTime $value) {
            return 'formatted:' . $value->format('Y-m-d');
        });

        $date = new DateTime('2026-01-02');

        $this->assertSame('formatted:2026-01-02', $compiler->applyEchoHandler($date));
    }

    public function testApplyEchoHandlerReturnsValueUnchangedWhenNoHandlerMatches() {
        $compiler = $this->compiler();

        $this->assertSame('plain', $compiler->applyEchoHandler('plain'));
    }
}
