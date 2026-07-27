<?php
use PHPUnit\Framework\TestCase;

class DynamicComponentTest extends TestCase {
    protected function tearDown(): void {
        CView_Component_AnonymousComponent::flushCache();
    }

    public function testConstructorStoresTheComponentName() {
        $component = new CView_Component_DynamicComponent('alert');

        $this->assertSame('alert', $component->component);
    }

    public function testRenderReturnsAClosure() {
        $component = new CView_Component_DynamicComponent('alert');

        $this->assertInstanceOf(Closure::class, $component->render());
    }

    public function testRenderedClosureWrapsTheTargetComponentTagWithBindingsAndSlots() {
        // compiler() caches its CView_Compiler_ComponentTagCompiler in a
        // static property built from CView::blade()'s aliases at first call
        // - it must be registered here before the first render() invocation
        // in this test run, or the cached compiler won't know this alias.
        CView::blade()->component(DynamicComponentTest_FakeAlert::class, 'dyn-alert');
        $component = new CView_Component_DynamicComponent('dyn-alert');
        $component->withAttributes(['type' => 'error', 'data-extra' => 'x']);

        $result = $component->render()(['__cview_slots' => [
            '__default' => new CView_ComponentSlot('body', []),
            'title' => new CView_ComponentSlot('Warning', []),
        ]]);

        $this->assertStringContainsString('<x-dyn-alert', $result);
        $this->assertStringContainsString('</x-dyn-alert>', $result);
        $this->assertStringContainsString("@props(['type'])", $result);
        $this->assertStringContainsString(':type="$type"', $result);
        $this->assertStringContainsString('{{ $attributes }}', $result);
        $this->assertStringContainsString('<x-slot name="title"', $result);
    }
}

class DynamicComponentTest_FakeAlert {
    public function __construct($type = null) {
    }
}
