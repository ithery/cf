<?php
use PHPUnit\Framework\TestCase;

class CompileJsonTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testJsonDirectiveUsesDefaultOptionsAndDepth() {
        $this->assertSame(
            '<?php echo json_encode($array, 15, 512) ?>',
            $this->compiler()->compileString('@json($array)')
        );
    }

    public function testJsonDirectiveWithCustomOptions() {
        $this->assertSame(
            '<?php echo json_encode($array, JSON_PRETTY_PRINT, 512) ?>',
            $this->compiler()->compileString('@json($array, JSON_PRETTY_PRINT)')
        );
    }

    public function testJsonDirectiveWithCustomOptionsAndDepth() {
        $this->assertSame(
            '<?php echo json_encode($array, JSON_PRETTY_PRINT, 3) ?>',
            $this->compiler()->compileString('@json($array, JSON_PRETTY_PRINT, 3)')
        );
    }

    public function testJsonAttrDirectiveEscapesForHtmlAttributes() {
        $this->assertSame(
            "<?php echo htmlspecialchars(json_encode(\$array, 15, 512), ENT_QUOTES, 'UTF-8') ?>",
            $this->compiler()->compileString('@jsonAttr($array)')
        );
    }
}
