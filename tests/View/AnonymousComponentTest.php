<?php
use PHPUnit\Framework\TestCase;

class AnonymousComponentTest extends TestCase {
    public function testRenderReturnsTheViewName() {
        $component = new CView_Component_AnonymousComponent('components.alert', []);

        $this->assertSame('components.alert', $component->render());
    }

    public function testDataIncludesTheConstructorDataAndAnAttributeBag() {
        $component = new CView_Component_AnonymousComponent('components.alert', ['type' => 'error']);

        $data = $component->data();

        $this->assertSame('error', $data['type']);
        $this->assertInstanceOf(CView_ComponentAttributeBag::class, $data['attributes']);
    }

    public function testDataReusesAnAttributeBagSetViaWithAttributes() {
        $component = new CView_Component_AnonymousComponent('components.alert', []);
        $component->withAttributes(['class' => 'font-bold']);

        $data = $component->data();

        $this->assertSame('font-bold', $data['attributes']->get('class'));
    }

    public function testConstructorDataTakesPrecedenceOverTheAttributesKey() {
        // "+" (array union) keeps the LEFT side's value on key conflicts,
        // so an explicit "attributes" entry in the constructor data wins
        // over the lazily-created default bag.
        $component = new CView_Component_AnonymousComponent('components.alert', ['attributes' => 'not-a-bag']);

        $this->assertSame('not-a-bag', $component->data()['attributes']);
    }
}
