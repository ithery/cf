<?php
use PHPUnit\Framework\TestCase;

class CompileInjectionTraitTest extends TestCase {
    protected function compiler() {
        return new CView_Compiler_BladeCompiler();
    }

    public function testInjectDirectiveIsCompiled() {
        $this->assertSame(
            '<?php $metrics = CContainer::getInstance()->make(\'MetricsService\'); ?>',
            $this->compiler()->compileString("@inject('metrics', 'MetricsService')")
        );
    }
}
