<?php
use PHPUnit\Framework\TestCase;

class ComponentAttributeBagTest extends TestCase {
    public function testGetReturnsAttributeOrDefault() {
        $bag = new CView_ComponentAttributeBag(['class' => 'font-bold']);

        $this->assertSame('font-bold', $bag->get('class'));
        $this->assertNull($bag->get('missing'));
        $this->assertSame('fallback', $bag->get('missing', 'fallback'));
    }

    public function testHasAndMissing() {
        $bag = new CView_ComponentAttributeBag(['class' => 'font-bold']);

        $this->assertTrue($bag->has('class'));
        $this->assertFalse($bag->has('id'));
        $this->assertTrue($bag->missing('id'));
        $this->assertFalse($bag->missing('class'));
    }

    public function testFirstReturnsTheFirstAttributeValue() {
        $bag = new CView_ComponentAttributeBag(['class' => 'font-bold', 'id' => 'alert']);

        $this->assertSame('font-bold', $bag->first());
    }

    public function testFirstReturnsDefaultWhenEmpty() {
        $bag = new CView_ComponentAttributeBag();

        $this->assertSame('fallback', $bag->first('fallback'));
    }

    public function testOnlyFiltersToGivenKeys() {
        $bag = new CView_ComponentAttributeBag(['class' => 'a', 'id' => 'b', 'name' => 'c']);

        $only = $bag->only(['class', 'id']);

        $this->assertInstanceOf(CView_ComponentAttributeBag::class, $only);
        $this->assertSame(['class' => 'a', 'id' => 'b'], $only->getAttributes());
    }

    public function testOnlyWithNullReturnsAllAttributes() {
        $bag = new CView_ComponentAttributeBag(['class' => 'a', 'id' => 'b']);

        $this->assertSame(['class' => 'a', 'id' => 'b'], $bag->only(null)->getAttributes());
    }

    public function testExceptExcludesGivenKeys() {
        $bag = new CView_ComponentAttributeBag(['class' => 'a', 'id' => 'b', 'name' => 'c']);

        $this->assertSame(['name' => 'c'], $bag->except(['class', 'id'])->getAttributes());
    }

    public function testFilterReturnsMatchingAttributes() {
        $bag = new CView_ComponentAttributeBag(['class' => 'a', 'data-foo' => 'b']);

        $filtered = $bag->filter(function ($value, $key) {
            return $key === 'class';
        });

        $this->assertSame(['class' => 'a'], $filtered->getAttributes());
    }

    public function testWhereStartsWithAndWhereDoesntStartWith() {
        $bag = new CView_ComponentAttributeBag(['wire:model' => 'a', 'wire:click' => 'b', 'class' => 'c']);

        $this->assertSame(
            ['wire:model' => 'a', 'wire:click' => 'b'],
            $bag->whereStartsWith('wire:')->getAttributes()
        );
        $this->assertSame(
            ['class' => 'c'],
            $bag->whereDoesntStartWith('wire:')->getAttributes()
        );
        $this->assertSame(
            $bag->whereStartsWith('wire:')->getAttributes(),
            $bag->thatStartWith('wire:')->getAttributes()
        );
    }

    public function testOnlyPropsAndExceptPropsMatchKebabAndCamelVariants() {
        $bag = new CView_ComponentAttributeBag(['max-width' => '10', 'maxWidth' => '20', 'class' => 'a']);

        $onlyProps = $bag->onlyProps(['maxWidth']);
        $this->assertSame(['max-width' => '10', 'maxWidth' => '20'], $onlyProps->getAttributes());

        $exceptProps = $bag->exceptProps(['maxWidth']);
        $this->assertSame(['class' => 'a'], $exceptProps->getAttributes());
    }

    public function testOnlyPropsSupportsDefaultValueKeyedArrays() {
        // extractPropNames() treats numeric-keyed entries as prop names
        // and string keys as [propName => defaultValue] pairs - both forms
        // are used when a component declares @props(['size' => 'md']).
        $bag = new CView_ComponentAttributeBag(['size' => 'lg', 'class' => 'a']);

        $onlyProps = $bag->onlyProps(['size' => 'md']);

        $this->assertSame(['size' => 'lg'], $onlyProps->getAttributes());
    }

    public function testClassMergesConditionalClasses() {
        // merge()/class() put their argument (typically the component's own
        // "default" classes) BEFORE the bag's pre-existing value (typically
        // what the caller passed in) in the concatenated result.
        $bag = new CView_ComponentAttributeBag(['class' => 'font-bold']);

        $merged = $bag->class(['ml-2', 'mr-2' => false, 'mt-4' => true]);

        $this->assertSame('ml-2 mt-4 font-bold', (string) $merged['class']);
    }

    public function testStyleMergesConditionalStyles() {
        $bag = new CView_ComponentAttributeBag(['style' => 'color: red;']);

        $merged = $bag->style(['display: none' => false, 'font-weight: bold;' => true]);

        $this->assertSame('font-weight: bold; color: red;', (string) $merged['style']);
    }

    public function testMergeAddsNewAttributes() {
        $bag = new CView_ComponentAttributeBag(['class' => 'a']);

        $merged = $bag->merge(['id' => 'alert']);

        $this->assertSame('a', $merged['class']);
        $this->assertSame('alert', $merged['id']);
    }

    public function testMergeAppendsClassAndStyleRatherThanOverwriting() {
        // Only the bag's pre-existing style value gets cstr::finish(';')
        // applied - the caller is expected to already terminate their own
        // default with a semicolon, hence 'display: none' (no trailing ';')
        // below staying exactly as passed.
        $bag = new CView_ComponentAttributeBag(['class' => 'mt-4', 'style' => 'color: red']);

        $merged = $bag->merge(['class' => 'font-bold', 'style' => 'display: none']);

        $this->assertSame('font-bold mt-4', $merged['class']);
        $this->assertSame('display: none color: red;', $merged['style']);
    }

    public function testMergeEscapesDefaultsByDefaultButNotBagValues() {
        $bag = new CView_ComponentAttributeBag(['title' => '<raw>']);

        $merged = $bag->merge(['title' => '<script>']);

        // Non-class/style keys are overwritten by the bag's own value, not
        // appended - and only the *default* passed to merge() is escaped.
        $this->assertSame('<raw>', $merged['title']);
    }

    public function testMergeWithEscapeFalseDoesNotEscapeDefaults() {
        $bag = new CView_ComponentAttributeBag();

        $merged = $bag->merge(['title' => '<script>'], false);

        $this->assertSame('<script>', $merged['title']);
    }

    public function testMergeDoesNotEscapeBooleanOrNullDefaults() {
        $bag = new CView_ComponentAttributeBag();

        $merged = $bag->merge(['disabled' => true, 'hidden' => null]);

        $this->assertTrue($merged['disabled']);
        $this->assertNull($merged['hidden']);
    }

    public function testPrependsAppendsBeforeTheBagsExistingClassValue() {
        $bag = new CView_ComponentAttributeBag(['class' => 'mt-4']);

        $merged = $bag->merge(['class' => $bag->prepends('font-bold')]);

        $this->assertSame('font-bold mt-4', $merged['class']);
    }

    public function testSetAttributesMergesInAParentAttributesBag() {
        $bag = new CView_ComponentAttributeBag();
        $parent = new CView_ComponentAttributeBag(['class' => 'mt-4']);

        $bag->setAttributes(['attributes' => $parent, 'class' => 'font-bold']);

        $this->assertSame('font-bold mt-4', $bag->get('class'));
    }

    public function testArrayAccessGetSetExistsUnset() {
        $bag = new CView_ComponentAttributeBag(['class' => 'a']);

        $this->assertTrue(isset($bag['class']));
        $this->assertSame('a', $bag['class']);

        $bag['id'] = 'alert';
        $this->assertSame('alert', $bag['id']);

        unset($bag['id']);
        $this->assertFalse(isset($bag['id']));
    }

    public function testGetIteratorAndForeach() {
        $bag = new CView_ComponentAttributeBag(['class' => 'a', 'id' => 'b']);

        $collected = [];
        foreach ($bag as $key => $value) {
            $collected[$key] = $value;
        }

        $this->assertSame(['class' => 'a', 'id' => 'b'], $collected);
    }

    public function testToStringRendersHtmlAttributes() {
        $bag = new CView_ComponentAttributeBag(['class' => 'a', 'id' => 'alert']);

        $this->assertSame('class="a" id="alert"', (string) $bag);
        $this->assertSame((string) $bag, $bag->toHtml());
    }

    public function testToStringSkipsFalseAndNullAttributes() {
        $bag = new CView_ComponentAttributeBag(['hidden' => false, 'disabled' => null, 'class' => 'a']);

        $this->assertSame('class="a"', (string) $bag);
    }

    public function testToStringRendersTrueBooleanAttributesAsTheirKeyName() {
        $bag = new CView_ComponentAttributeBag(['required' => true]);

        $this->assertSame('required="required"', (string) $bag);
    }

    public function testToStringEscapesDoubleQuotesInValues() {
        $bag = new CView_ComponentAttributeBag(['title' => 'say "hi"']);

        $this->assertSame('title="say \\"hi\\""', (string) $bag);
    }

    public function testInvokeReturnsHtmlStringOfMergedAttributes() {
        $bag = new CView_ComponentAttributeBag(['class' => 'a']);

        $result = $bag(['id' => 'alert']);

        $this->assertInstanceOf(CBase_HtmlString::class, $result);
        $this->assertSame('id="alert" class="a"', (string) $result);
    }
}
