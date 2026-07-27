<?php
use PHPUnit\Framework\TestCase;

class IconComponentTest extends TestCase {
    public function testConstructorStoresAttributesWithDefaults() {
        $component = new CView_Component_IconComponent('missing/icon.svg', 'my-id');

        $this->assertSame('my-id', $component->id);
        $this->assertNull($component->class);
        $this->assertSame('1em', $component->width);
        $this->assertSame('1em', $component->height);
        $this->assertSame('img', $component->role);
        $this->assertSame('currentColor', $component->fill);
    }

    public function testConstructorAcceptsAllOverrides() {
        $component = new CView_Component_IconComponent(
            'missing/icon.svg',
            'my-id',
            'h-4 w-4',
            '2em',
            '2em',
            'presentation',
            'red'
        );

        $this->assertSame('h-4 w-4', $component->class);
        $this->assertSame('2em', $component->width);
        $this->assertSame('2em', $component->height);
        $this->assertSame('presentation', $component->role);
        $this->assertSame('red', $component->fill);
    }

    public function testRenderReturnsEmptyIconHtmlWhenTheIconFileDoesNotExist() {
        // CManager_Icon::loadFile() gracefully returns null for a missing
        // icon path (via c::optional($file)->getContents()), and
        // IconComponent::render() short-circuits to an empty string rather
        // than trying to parse null as XML.
        $component = new CView_Component_IconComponent('totally/missing/icon-' . uniqid() . '.svg');

        $result = $component->render();

        $this->assertInstanceOf(CManager_Icon_IconHtml::class, $result);
        $this->assertInstanceOf(CInterface_Htmlable::class, $result);
        $this->assertSame('', $result->toHtml());
    }
}
