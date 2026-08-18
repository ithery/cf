<?php

use PHPUnit\Framework\TestCase;

/**
 * Penguraian aturan dan perluasan wildcard.
 *
 * Sebelum satu aturan pun dijalankan, `items.*.id` harus lebih dulu menjadi
 * `items.0.id` dan `items.1.id` sesuai isi data yang sebenarnya. Perluasan
 * yang meleset tidak melempar apa-apa - ia hanya membuat aturannya tidak
 * pernah dijalankan atas baris yang terlewat, dan data buruk lolos diam-diam.
 */
class ValidationRuleParserTest extends TestCase {
    /**
     * @return void
     */
    public function testParseSplitsARuleIntoNameAndParameters() {
        $this->assertSame(['Required', []], CValidation_RuleParser::parse('required'));
        $this->assertSame(['Max', ['10']], CValidation_RuleParser::parse('max:10'));
        $this->assertSame(['Between', ['1', '5']], CValidation_RuleParser::parse('between:1,5'));
    }

    /**
     * @return void
     */
    public function testParseUnquotesTheValuesOfAnInRule() {
        $this->assertSame(['In', ['a', 'b']], CValidation_RuleParser::parse('in:"a","b"'));
    }

    /**
     * Koma di dalam sebuah regex bukan pemisah parameter. Tanpa perlakuan
     * khusus, `/^a,b$/` akan pecah menjadi dua parameter dan polanya rusak
     * tanpa satu pun galat.
     *
     * @return void
     */
    public function testACommaInsideARegexIsNotAParameterSeparator() {
        $this->assertSame(['Regex', ['/^a,b$/']], CValidation_RuleParser::parse('regex:/^a,b$/'));
    }

    /**
     * @return void
     */
    public function testRuleNamesComeBackInStudlyCase() {
        list($name) = CValidation_RuleParser::parse('required_if:a,b');

        $this->assertSame('RequiredIf', $name);
    }

    /**
     * @return void
     */
    public function testWildcardExpandsToTheIndexesThatActuallyExist() {
        $parser = new CValidation_RuleParser(['items' => [['id' => 1], ['id' => 2]]]);

        $exploded = $parser->explode(['items.*.id' => 'required|integer']);

        $this->assertSame(
            ['items.0.id' => ['required', 'integer'], 'items.1.id' => ['required', 'integer']],
            $exploded->rules
        );
    }

    /**
     * Pemetaan balik dari pola ke atribut nyata itulah yang dipakai saat
     * menyusun pesan galat, supaya pesannya menyebut baris yang benar.
     *
     * @return void
     */
    public function testExpansionRecordsWhichAttributesCameFromTheWildcard() {
        $parser = new CValidation_RuleParser(['items' => [['id' => 1], ['id' => 2]]]);

        $exploded = $parser->explode(['items.*.id' => 'required']);

        $this->assertSame(
            ['items.*.id' => ['items.0.id', 'items.1.id']],
            $exploded->implicitAttributes
        );
    }

    /**
     * @return void
     */
    public function testWildcardOverAnEmptyListExpandsToNothing() {
        $parser = new CValidation_RuleParser(['items' => []]);

        $this->assertSame([], $parser->explode(['items.*.id' => 'required'])->rules);
    }

    /**
     * @return void
     */
    public function testAPlainAttributeIsLeftAloneApartFromSplitting() {
        $parser = new CValidation_RuleParser(['name' => 'budi']);

        $this->assertSame(
            ['name' => ['required', 'string']],
            $parser->explode(['name' => 'required|string'])->rules
        );
    }

    /**
     * @return void
     */
    public function testNestedRulesPrefixEachReturnedKeyWithTheAttribute() {
        $nested = new CValidation_NestedRules(function () {
            return ['nama' => 'required', 'umur' => 'integer'];
        });

        $compiled = $nested->compile('orang', ['nama' => 'x'], ['orang' => ['nama' => 'x']]);

        $this->assertSame(
            ['orang.nama' => ['required'], 'orang.umur' => ['integer']],
            $compiled->rules
        );
    }

    /**
     * @return void
     */
    public function testNestedRulesReturningAFlatRuleAppliesItToTheAttributeItself() {
        $nested = new CValidation_NestedRules(function () {
            return 'required|string';
        });

        $compiled = $nested->compile('judul', 'x', ['judul' => 'x']);

        $this->assertSame(['judul' => ['required', 'string']], $compiled->rules);
    }

    /**
     * @return void
     */
    public function testNestedRulesCallbackSeesTheValueAttributeAndWholeData() {
        $terlihat = [];
        $nested = new CValidation_NestedRules(function ($value, $attribute, $data) use (&$terlihat) {
            $terlihat = [$value, $attribute, $data];

            return 'required';
        });

        $nested->compile('judul', 'isi', ['judul' => 'isi']);

        $this->assertSame(['isi', 'judul', ['judul' => 'isi']], $terlihat);
    }

    /**
     * @return void
     */
    public function testGatherDataFlattensTheAddressedValue() {
        $data = ['user' => ['name' => 'budi']];

        $this->assertSame(
            ['user.name' => 'budi'],
            CValidation_Data::initializeAndGatherData('user.name', $data)
        );
    }

    /**
     * Bentuk datar itu memuat tiap elemen dan juga koleksi induknya, sebab
     * aturan dapat menyasar keduanya.
     *
     * @return void
     */
    public function testGatherDataOverAWildcardKeepsBothElementsAndTheirParent() {
        $data = ['user' => ['tags' => ['a', 'b']]];

        $this->assertSame(
            ['user.tags.0' => 'a', 'user.tags.1' => 'b', 'user.tags' => ['a', 'b']],
            CValidation_Data::initializeAndGatherData('user.*', $data)
        );
    }

    /**
     * @return void
     */
    public function testExtractDataFromPathKeepsTheOriginalShape() {
        $data = ['user' => ['name' => 'budi', 'age' => 30]];

        $this->assertSame(
            ['user' => ['name' => 'budi']],
            CValidation_Data::extractDataFromPath('user.name', $data)
        );
    }

    /**
     * Bagian sebelum wildcard pertama - dipakai untuk mencari data induk yang
     * perlu ditelusuri.
     *
     * @return void
     */
    public function testLeadingExplicitPathStopsAtTheFirstWildcard() {
        $this->assertSame('user', CValidation_Data::getLeadingExplicitAttributePath('user.*.x'));
        $this->assertSame('a.b.c', CValidation_Data::getLeadingExplicitAttributePath('a.b.c'));
        $this->assertNull(CValidation_Data::getLeadingExplicitAttributePath('*.x'));
    }
}
