<?php

use PHPUnit\Framework\TestCase;

class SupportHtmlStringTest extends TestCase {
    /**
     * HtmlString itu janji bahwa isinya sudah aman; itulah yang membuat
     * `c::e()` melewatkannya alih-alih meng-escape ulang.
     */
    public function testTheHtmlIsHandedBackUntouched() {
        $html = new CBase_HtmlString('<b>tebal</b>');

        $this->assertSame('<b>tebal</b>', $html->toHtml());
        $this->assertSame('<b>tebal</b>', (string) $html);
    }

    public function testAnEmptyStringIsRecognised() {
        $this->assertTrue((new CBase_HtmlString(''))->isEmpty());
        $this->assertFalse((new CBase_HtmlString(''))->isNotEmpty());
    }

    public function testANonEmptyStringIsRecognised() {
        $this->assertTrue((new CBase_HtmlString('<b>tebal</b>'))->isNotEmpty());
        $this->assertFalse((new CBase_HtmlString('<b>tebal</b>'))->isEmpty());
    }

    public function testTheDefaultIsAnEmptyString() {
        $this->assertSame('', (new CBase_HtmlString())->toHtml());
    }

    public function testEscapingSkipsAnHtmlString() {
        $this->assertSame('<b>tebal</b>', c::e(new CBase_HtmlString('<b>tebal</b>')));
        $this->assertSame('&lt;b&gt;tebal&lt;/b&gt;', c::e('<b>tebal</b>'));
    }

    /**
     * Js membangun ungkapan JavaScript yang aman ditaruh di dalam atribut HTML,
     * jadi tanda kutip dan kurung sudutnya harus sudah dilarikan.
     */
    public function testAnArrayBecomesAParsableJsonExpression() {
        $js = (string) CBase_Js::from(['nama' => 'Hery']);

        $this->assertStringStartsWith('JSON.parse(', $js);
        $this->assertStringContainsString('Hery', $js);
        $this->assertStringNotContainsString('"', $js);
    }

    public function testAnEmptyArrayStaysAPlainLiteral() {
        $this->assertSame('[]', (string) CBase_Js::from([]));
    }

    public function testDangerousCharactersAreEscapedIntoUnicodeSequences() {
        $js = (string) CBase_Js::from(['html' => '<script>alert(1)</script>']);

        //kurung sudutnya keluar sebagai \u003C, jadi tidak ada tag yang lahir
        //kembali ketika ungkapan ini ditaruh di dalam halaman
        $this->assertStringNotContainsString('<', $js);
        $this->assertStringContainsString('u003C', $js);
    }

    public function testANumberAndABooleanStayLiterals() {
        $this->assertSame('1', (string) CBase_Js::from(1));
        $this->assertSame('true', (string) CBase_Js::from(true));
        $this->assertSame('null', (string) CBase_Js::from(null));
    }

    public function testEncodeGivesTheJsonWithoutTheJavaScriptWrapper() {
        $this->assertSame('{"nama":"Hery"}', CBase_Js::encode(['nama' => 'Hery']));
    }

    public function testJsAlsoRendersItselfAsHtml() {
        $js = CBase_Js::from(['nama' => 'Hery']);

        $this->assertSame($js->toHtml(), (string) $js);
    }
}
