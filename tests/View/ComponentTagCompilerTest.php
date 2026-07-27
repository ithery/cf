<?php
use PHPUnit\Framework\TestCase;

/**
 * Covers CView_Compiler_ComponentTagCompiler - the `<x-component>`/`<x-slot>`
 * HTML tag syntax compiler that CView_Compiler_BladeCompiler::compileString()
 * runs as a precompile step before the "@directive" pipeline.
 */
class ComponentTagCompilerTest extends TestCase {
    protected function compiler(array $aliases = [], array $namespaces = []) {
        return new CView_Compiler_ComponentTagCompiler($aliases, $namespaces, new CView_Compiler_BladeCompiler());
    }

    // -----------------------------------------------------------------
    // Slots
    // -----------------------------------------------------------------

    public function testSimpleNamedSlotIsCompiled() {
        $result = $this->compiler()->compileSlots('<x-slot name="title">Hello</x-slot>');

        $this->assertSame(" @slot('title', null, []) Hello @endslot", $result);
    }

    public function testInlineNamedSlotIsCompiled() {
        $result = $this->compiler()->compileSlots('<x-slot:title>Hello</x-slot:title>');

        $this->assertSame(" @slot('title', null, []) Hello @endslot", $result);
    }

    public function testInlineNamedSlotWithHyphenIsCamelCased() {
        $result = $this->compiler()->compileSlots('<x-slot:foo-bar>Hello</x-slot:foo-bar>');

        $this->assertSame(" @slot('fooBar', null, []) Hello @endslot", $result);
    }

    public function testSlotWithExtraAttributesIsCompiled() {
        $result = $this->compiler()->compileSlots('<x-slot name="title" class="font-bold">Hello</x-slot>');

        $this->assertSame(" @slot('title', null, ['class' => 'font-bold']) Hello @endslot", $result);
    }

    // -----------------------------------------------------------------
    // Public string helpers
    // -----------------------------------------------------------------

    public function testStripQuotesRemovesSurroundingDoubleOrSingleQuotes() {
        $compiler = $this->compiler();

        $this->assertSame('title', $compiler->stripQuotes('"title"'));
        $this->assertSame('title', $compiler->stripQuotes("'title'"));
        $this->assertSame('title', $compiler->stripQuotes('title'));
    }

    public function testFormatClassNameConvertsDotNotationToStudlyNamespace() {
        $compiler = $this->compiler();

        $this->assertSame('Alert', $compiler->formatClassName('alert'));
        $this->assertSame('Forms\\InputGroup', $compiler->formatClassName('forms.input-group'));
    }

    public function testGuessClassNameBuildsAFullyQualifiedComponentClassName() {
        $compiler = $this->compiler();

        $this->assertSame('\\CView_Component_AlertComponent', $compiler->guessClassName('alert'));
    }

    public function testGuessViewNameDefaultsToComponentPrefix() {
        $compiler = $this->compiler();

        $this->assertSame('component.alert', $compiler->guessViewName('alert'));
        $this->assertSame('forms.alert', $compiler->guessViewName('alert', 'forms'));
    }

    // -----------------------------------------------------------------
    // componentClass() resolution
    // -----------------------------------------------------------------

    public function testComponentClassResolvesARegisteredClassAlias() {
        $compiler = $this->compiler(['alert' => ComponentTagCompilerTest_FakeAlert::class]);

        $this->assertSame(ComponentTagCompilerTest_FakeAlert::class, $compiler->componentClass('alert'));
    }

    public function testComponentClassThrowsWhenAliasedTargetDoesNotExist() {
        $compiler = $this->compiler(['alert' => 'Totally\\Missing\\ClassOrView']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to locate class or view [Totally\\Missing\\ClassOrView] for component [alert].');

        $compiler->componentClass('alert');
    }

    public function testComponentClassThrowsForACompletelyUnknownComponent() {
        $compiler = $this->compiler();

        $this->expectException(InvalidArgumentException::class);

        $compiler->componentClass('totally-unknown-component-xyz');
    }

    public function testComponentClassAllowsMailPrefixedComponentsToPassThrough() {
        $compiler = $this->compiler();

        $this->assertSame('mail::totally-unknown', $compiler->componentClass('mail::totally-unknown'));
    }

    // -----------------------------------------------------------------
    // Full tag compilation (using a real, resolvable fake component class)
    // -----------------------------------------------------------------

    public function testSelfClosingComponentTagIsCompiled() {
        // "type" matches ComponentTagCompilerTest_FakeAlert's constructor
        // parameter name, so partitionDataAndAttributes() routes it into the
        // @component(...) constructor-data array rather than the trailing
        // $component->withAttributes([...]) call.
        $compiler = $this->compiler(['alert' => ComponentTagCompilerTest_FakeAlert::class]);

        $result = $compiler->compile('<x-alert type="error" />');

        $this->assertStringContainsString(
            "##BEGIN-COMPONENT-CLASS##@component('" . ComponentTagCompilerTest_FakeAlert::class . "', 'alert', ['type' => 'error'])",
            $result
        );
        $this->assertStringContainsString('$component->withAttributes([]);', $result);
        $this->assertStringContainsString('@endComponentClass##END-COMPONENT-CLASS##', $result);
    }

    public function testPairedComponentTagCompilesOpeningAndClosingSeparately() {
        $compiler = $this->compiler(['alert' => ComponentTagCompilerTest_FakeAlert::class]);

        $result = $compiler->compile('<x-alert>Body</x-alert>');

        $this->assertStringContainsString("@component('" . ComponentTagCompilerTest_FakeAlert::class . "', 'alert',", $result);
        $this->assertStringContainsString('Body', $result);
        $this->assertStringContainsString('@endComponentClass##END-COMPONENT-CLASS##', $result);
    }

    public function testBoundAttributeIsPassedThroughSanitizeComponentAttribute() {
        // Only attributes that DON'T match a constructor parameter name end
        // up in the escaped $component->withAttributes([...]) call - "label"
        // isn't one of FakeAlert's constructor params, unlike "type".
        $compiler = $this->compiler(['alert' => ComponentTagCompilerTest_FakeAlert::class]);

        $result = $compiler->compile('<x-alert :label="$label" />');

        $this->assertStringContainsString(
            "'label' => \CView_Compiler_BladeCompiler::sanitizeComponentAttribute(\$label)",
            $result
        );
    }

    public function testClassDirectiveOnAComponentTagIsCompiledToCssClasses() {
        $compiler = $this->compiler(['alert' => ComponentTagCompilerTest_FakeAlert::class]);

        $result = $compiler->compile("<x-alert @class(['font-bold' => \$active]) />");

        $this->assertStringContainsString("\carr::toCssClasses(['font-bold' => \$active])", $result);
    }

    public function testUnknownComponentTagThrows() {
        $compiler = $this->compiler();

        $this->expectException(InvalidArgumentException::class);

        $compiler->compile('<x-totally-unknown-xyz />');
    }
}

class ComponentTagCompilerTest_FakeAlert {
    public function __construct($type = null) {
        $this->type = $type;
    }
}
