<?php
use PHPUnit\Framework\TestCase;

class TemplateComponentTest extends TestCase {
    public function testRenderReturnsTheRawTemplateString() {
        $component = new CView_Component_TemplateComponent('<div>{{ $slot }}</div>');

        $this->assertSame('<div>{{ $slot }}</div>', $component->render());
    }

    public function testResolveViewCompilesTheTemplateIntoARealBladeView() {
        $component = new CView_Component_TemplateComponent('<b>hello</b>');

        $resolved = $component->resolveView();

        $this->assertStringStartsWith('__components::', $resolved);
        $this->assertTrue(CView::factory()->exists($resolved));
    }
}
