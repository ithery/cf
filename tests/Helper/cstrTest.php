<?php

use Ramsey\Uuid\UuidInterface;
use PHPUnit\Framework\TestCase;

// @codingStandardsIgnoreStart
class cstrTest extends TestCase {
    //@codingStandardsIgnoreEnd

    /**
     * @return void
     */
    public function testStringCanBeLimitedByWords() {
        $this->assertSame('Taylor...', cstr::words('Taylor Otwell', 1));
        $this->assertSame('Taylor___', cstr::words('Taylor Otwell', 1, '___'));
        $this->assertSame('Taylor Otwell', cstr::words('Taylor Otwell', 3));
    }

    public function testStringCanBeLimitedByWordsNonAscii() {
        $this->assertSame('这是...', cstr::words('这是 段中文', 1));
        $this->assertSame('这是___', cstr::words('这是 段中文', 1, '___'));
        $this->assertSame('这是-段中文', cstr::words('这是-段中文', 3, '___'));
        $this->assertSame('这是___', cstr::words('这是     段中文', 1, '___'));
    }

    public function testStringTrimmedOnlyWhereNecessary() {
        $this->assertSame(' Taylor Otwell ', cstr::words(' Taylor Otwell ', 3));
        $this->assertSame(' Taylor...', cstr::words(' Taylor Otwell ', 1));
    }

    public function testStringTitle() {
        $this->assertSame('Jefferson Costella', cstr::title('jefferson costella'));
        $this->assertSame('Jefferson Costella', cstr::title('jefFErson coSTella'));
    }

    public function testStringHeadline() {
        $this->assertSame('Jefferson Costella', cstr::headline('jefferson costella'));
        $this->assertSame('Jefferson Costella', cstr::headline('jefFErson coSTella'));
        $this->assertSame('Jefferson Costella Uses Laravel', cstr::headline('jefferson_costella uses-_Laravel'));
        $this->assertSame('Jefferson Costella Uses Laravel', cstr::headline('jefferson_costella uses__Laravel'));

        $this->assertSame('Laravel P H P Framework', cstr::headline('laravel_p_h_p_framework'));
        $this->assertSame('Laravel P H P Framework', cstr::headline('laravel _p _h _p _framework'));
        $this->assertSame('Laravel Php Framework', cstr::headline('laravel_php_framework'));
        $this->assertSame('Laravel Ph P Framework', cstr::headline('laravel-phP-framework'));
        $this->assertSame('Laravel Php Framework', cstr::headline('laravel  -_-  php   -_-   framework   '));

        $this->assertSame('Foo Bar', cstr::headline('fooBar'));
        $this->assertSame('Foo Bar', cstr::headline('foo_bar'));
        $this->assertSame('Foo Bar Baz', cstr::headline('foo-barBaz'));
        $this->assertSame('Foo Bar Baz', cstr::headline('foo-bar_baz'));
    }

    public function testStringWithoutWordsDoesntProduceError() {
        $nbsp = chr(0xC2) . chr(0xA0);
        $this->assertSame(' ', cstr::words(' '));
        $this->assertEquals($nbsp, cstr::words($nbsp));
    }

    public function testStringAscii() {
        $this->assertSame('@', cstr::ascii('@'));
        $this->assertSame('u', cstr::ascii('ü'));
    }

    public function testStringAsciiWithSpecificLocale() {
        $this->assertSame('h H sht Sht a A ia yo', cstr::ascii('х Х щ Щ ъ Ъ иа йо', 'bg'));
        $this->assertSame('ae oe ue Ae Oe Ue', cstr::ascii('ä ö ü Ä Ö Ü', 'de'));
    }

    public function testStartsWith() {
        $this->assertTrue(cstr::startsWith('jason', 'jas'));
        $this->assertTrue(cstr::startsWith('jason', 'jason'));
        $this->assertTrue(cstr::startsWith('jason', ['jas']));
        $this->assertTrue(cstr::startsWith('jason', ['day', 'jas']));
        $this->assertFalse(cstr::startsWith('jason', 'day'));
        $this->assertFalse(cstr::startsWith('jason', ['day']));
        $this->assertFalse(cstr::startsWith('jason', null));
        $this->assertFalse(cstr::startsWith('jason', [null]));
        $this->assertFalse(cstr::startsWith('0123', [null]));
        $this->assertTrue(cstr::startsWith('0123', 0));
        $this->assertFalse(cstr::startsWith('jason', 'J'));
        $this->assertFalse(cstr::startsWith('jason', ''));
        $this->assertFalse(cstr::startsWith('', ''));
        $this->assertFalse(cstr::startsWith('7', ' 7'));
        $this->assertTrue(cstr::startsWith('7a', '7'));
        $this->assertTrue(cstr::startsWith('7a', 7));
        $this->assertTrue(cstr::startsWith('7.12a', 7.12));
        $this->assertFalse(cstr::startsWith('7.12a', 7.13));
        $this->assertTrue(cstr::startsWith(7.123, '7'));
        $this->assertTrue(cstr::startsWith(7.123, '7.12'));
        $this->assertFalse(cstr::startsWith(7.123, '7.13'));
        // Test for multibyte string support
        $this->assertTrue(cstr::startsWith('Jönköping', 'Jö'));
        $this->assertTrue(cstr::startsWith('Malmö', 'Malmö'));
        $this->assertFalse(cstr::startsWith('Jönköping', 'Jonko'));
        $this->assertFalse(cstr::startsWith('Malmö', 'Malmo'));
        $this->assertTrue(cstr::startsWith('你好', '你'));
        $this->assertFalse(cstr::startsWith('你好', '好'));
        $this->assertFalse(cstr::startsWith('你好', 'a'));
    }

    public function testEndsWith() {
        $this->assertTrue(cstr::endsWith('jason', 'on'));
        $this->assertTrue(cstr::endsWith('jason', 'jason'));
        $this->assertTrue(cstr::endsWith('jason', ['on']));
        $this->assertTrue(cstr::endsWith('jason', ['no', 'on']));
        $this->assertFalse(cstr::endsWith('jason', 'no'));
        $this->assertFalse(cstr::endsWith('jason', ['no']));
        $this->assertFalse(cstr::endsWith('jason', ''));
        $this->assertFalse(cstr::endsWith('', ''));
        $this->assertFalse(cstr::endsWith('jason', [null]));
        $this->assertFalse(cstr::endsWith('jason', null));
        $this->assertFalse(cstr::endsWith('jason', 'N'));
        $this->assertFalse(cstr::endsWith('7', ' 7'));
        $this->assertTrue(cstr::endsWith('a7', '7'));
        $this->assertTrue(cstr::endsWith('a7', 7));
        $this->assertTrue(cstr::endsWith('a7.12', 7.12));
        $this->assertFalse(cstr::endsWith('a7.12', 7.13));
        $this->assertTrue(cstr::endsWith(0.27, '7'));
        $this->assertTrue(cstr::endsWith(0.27, '0.27'));
        $this->assertFalse(cstr::endsWith(0.27, '8'));
        // Test for multibyte string support
        $this->assertTrue(cstr::endsWith('Jönköping', 'öping'));
        $this->assertTrue(cstr::endsWith('Malmö', 'mö'));
        $this->assertFalse(cstr::endsWith('Jönköping', 'oping'));
        $this->assertFalse(cstr::endsWith('Malmö', 'mo'));
        $this->assertTrue(cstr::endsWith('你好', '好'));
        $this->assertFalse(cstr::endsWith('你好', '你'));
        $this->assertFalse(cstr::endsWith('你好', 'a'));
    }

    public function testStrBefore() {
        $this->assertSame('han', cstr::before('hannah', 'nah'));
        $this->assertSame('ha', cstr::before('hannah', 'n'));
        $this->assertSame('ééé ', cstr::before('ééé hannah', 'han'));
        $this->assertSame('hannah', cstr::before('hannah', 'xxxx'));
        $this->assertSame('hannah', cstr::before('hannah', ''));
        $this->assertSame('han', cstr::before('han0nah', '0'));
        $this->assertSame('han', cstr::before('han0nah', 0));
        $this->assertSame('han', cstr::before('han2nah', 2));
    }

    public function testStrBeforeLast() {
        $this->assertSame('yve', cstr::beforeLast('yvette', 'tte'));
        $this->assertSame('yvet', cstr::beforeLast('yvette', 't'));
        $this->assertSame('ééé ', cstr::beforeLast('ééé yvette', 'yve'));
        $this->assertSame('', cstr::beforeLast('yvette', 'yve'));
        $this->assertSame('yvette', cstr::beforeLast('yvette', 'xxxx'));
        $this->assertSame('yvette', cstr::beforeLast('yvette', ''));
        $this->assertSame('yv0et', cstr::beforeLast('yv0et0te', '0'));
        $this->assertSame('yv0et', cstr::beforeLast('yv0et0te', 0));
        $this->assertSame('yv2et', cstr::beforeLast('yv2et2te', 2));
    }

    public function testStrBetween() {
        $this->assertSame('abc', cstr::between('abc', '', 'c'));
        $this->assertSame('abc', cstr::between('abc', 'a', ''));
        $this->assertSame('abc', cstr::between('abc', '', ''));
        $this->assertSame('b', cstr::between('abc', 'a', 'c'));
        $this->assertSame('b', cstr::between('dddabc', 'a', 'c'));
        $this->assertSame('b', cstr::between('abcddd', 'a', 'c'));
        $this->assertSame('b', cstr::between('dddabcddd', 'a', 'c'));
        $this->assertSame('nn', cstr::between('hannah', 'ha', 'ah'));
        $this->assertSame('a]ab[b', cstr::between('[a]ab[b]', '[', ']'));
        $this->assertSame('foo', cstr::between('foofoobar', 'foo', 'bar'));
        $this->assertSame('bar', cstr::between('foobarbar', 'foo', 'bar'));
    }

    public function testStrAfter() {
        $this->assertSame('nah', cstr::after('hannah', 'han'));
        $this->assertSame('nah', cstr::after('hannah', 'n'));
        $this->assertSame('nah', cstr::after('ééé hannah', 'han'));
        $this->assertSame('hannah', cstr::after('hannah', 'xxxx'));
        $this->assertSame('hannah', cstr::after('hannah', ''));
        $this->assertSame('nah', cstr::after('han0nah', '0'));
        $this->assertSame('nah', cstr::after('han0nah', 0));
        $this->assertSame('nah', cstr::after('han2nah', 2));
    }

    public function testStrAfterLast() {
        $this->assertSame('tte', cstr::afterLast('yvette', 'yve'));
        $this->assertSame('e', cstr::afterLast('yvette', 't'));
        $this->assertSame('e', cstr::afterLast('ééé yvette', 't'));
        $this->assertSame('', cstr::afterLast('yvette', 'tte'));
        $this->assertSame('yvette', cstr::afterLast('yvette', 'xxxx'));
        $this->assertSame('yvette', cstr::afterLast('yvette', ''));
        $this->assertSame('te', cstr::afterLast('yv0et0te', '0'));
        $this->assertSame('te', cstr::afterLast('yv0et0te', 0));
        $this->assertSame('te', cstr::afterLast('yv2et2te', 2));
        $this->assertSame('foo', cstr::afterLast('----foo', '---'));
    }

    public function testStrContains() {
        $this->assertTrue(cstr::contains('taylor', 'ylo'));
        $this->assertTrue(cstr::contains('taylor', 'taylor'));
        $this->assertTrue(cstr::contains('taylor', ['ylo']));
        $this->assertTrue(cstr::contains('taylor', ['xxx', 'ylo']));
        $this->assertFalse(cstr::contains('taylor', 'xxx'));
        $this->assertFalse(cstr::contains('taylor', ['xxx']));
        $this->assertFalse(cstr::contains('taylor', ''));
        $this->assertFalse(cstr::contains('', ''));
    }

    public function testStrContainsAll() {
        $this->assertTrue(cstr::containsAll('taylor otwell', ['taylor', 'otwell']));
        $this->assertTrue(cstr::containsAll('taylor otwell', ['taylor']));
        $this->assertFalse(cstr::containsAll('taylor otwell', ['taylor', 'xxx']));
    }

    public function testParseCallback() {
        $this->assertEquals(['Class', 'method'], cstr::parseCallback('Class@method', 'foo'));
        $this->assertEquals(['Class', 'foo'], cstr::parseCallback('Class', 'foo'));
        $this->assertEquals(['Class', null], cstr::parseCallback('Class'));
    }

    public function testSlug() {
        $this->assertSame('hello-world', cstr::slug('hello world'));
        $this->assertSame('hello-world', cstr::slug('hello-world'));
        $this->assertSame('hello-world', cstr::slug('hello_world'));
        $this->assertSame('hello_world', cstr::slug('hello_world', '_'));
        $this->assertSame('user-at-host', cstr::slug('user@host'));
        $this->assertSame('سلام-دنیا', cstr::slug('سلام دنیا', '-', null));
        $this->assertSame('sometext', cstr::slug('some text', ''));
        $this->assertSame('', cstr::slug('', ''));
        $this->assertSame('', cstr::slug(''));
    }

    public function testStrStart() {
        $this->assertSame('/test/string', cstr::start('test/string', '/'));
        $this->assertSame('/test/string', cstr::start('/test/string', '/'));
        $this->assertSame('/test/string', cstr::start('//test/string', '/'));
    }

    public function testFinish() {
        $this->assertSame('abbc', cstr::finish('ab', 'bc'));
        $this->assertSame('abbc', cstr::finish('abbcbc', 'bc'));
        $this->assertSame('abcbbc', cstr::finish('abcbbcbc', 'bc'));
    }

    public function testIs() {
        $this->assertTrue(cstr::is('/', '/'));
        $this->assertFalse(cstr::is('/', ' /'));
        $this->assertFalse(cstr::is('/', '/a'));
        $this->assertTrue(cstr::is('foo/*', 'foo/bar/baz'));

        $this->assertTrue(cstr::is('*@*', 'App\Class@method'));
        $this->assertTrue(cstr::is('*@*', 'app\Class@'));
        $this->assertTrue(cstr::is('*@*', '@method'));

        // is case sensitive
        $this->assertFalse(cstr::is('*BAZ*', 'foo/bar/baz'));
        $this->assertFalse(cstr::is('*FOO*', 'foo/bar/baz'));
        $this->assertFalse(cstr::is('A', 'a'));

        // Accepts array of patterns
        $this->assertTrue(cstr::is(['a*', 'b*'], 'a/'));
        $this->assertTrue(cstr::is(['a*', 'b*'], 'b/'));
        $this->assertFalse(cstr::is(['a*', 'b*'], 'f/'));

        // numeric values and patterns
        $this->assertFalse(cstr::is(['a*', 'b*'], 123));
        $this->assertTrue(cstr::is(['*2*', 'b*'], 11211));

        $this->assertTrue(cstr::is('*/foo', 'blah/baz/foo'));

        $valueObject = new StringableObjectStub('foo/bar/baz');
        $patternObject = new StringableObjectStub('foo/*');

        $this->assertTrue(cstr::is('foo/bar/baz', $valueObject));
        $this->assertTrue(cstr::is($patternObject, $valueObject));

        // empty patterns
        $this->assertFalse(cstr::is([], 'test'));

        $this->assertFalse(cstr::is('', 0));
        $this->assertFalse(cstr::is([null], 0));
        $this->assertTrue(cstr::is([null], null));
    }

    /**
     * @param mixed $uuid
     * @dataProvider validUuidList
     */
    public function testIsUuidWithValidUuid($uuid) {
        $this->assertTrue(cstr::isUuid($uuid));
    }

    /**
     * @param mixed $uuid
     * @dataProvider invalidUuidList
     */
    public function testIsUuidWithInvalidUuid($uuid) {
        $this->assertFalse(cstr::isUuid($uuid));
    }

    public function testKebab() {
        $this->assertSame('laravel-php-framework', cstr::kebab('LaravelPhpFramework'));
    }

    public function testLower() {
        $this->assertSame('foo bar baz', cstr::lower('FOO BAR BAZ'));
        $this->assertSame('foo bar baz', cstr::lower('fOo Bar bAz'));
    }

    public function testUpper() {
        $this->assertSame('FOO BAR BAZ', cstr::upper('foo bar baz'));
        $this->assertSame('FOO BAR BAZ', cstr::upper('foO bAr BaZ'));
    }

    public function testLimit() {
        $this->assertSame('Laravel is...', cstr::limit('Laravel is a free, open source PHP web application framework.', 10));
        $this->assertSame('这是一...', cstr::limit('这是一段中文', 6));

        $string = 'The PHP framework for web artisans.';
        $this->assertSame('The PHP...', cstr::limit($string, 7));
        $this->assertSame('The PHP', cstr::limit($string, 7, ''));
        $this->assertSame('The PHP framework for web artisans.', cstr::limit($string, 100));

        $nonAsciiString = '这是一段中文';
        $this->assertSame('这是一...', cstr::limit($nonAsciiString, 6));
        $this->assertSame('这是一', cstr::limit($nonAsciiString, 6, ''));
    }

    public function testLength() {
        $this->assertEquals(11, cstr::length('foo bar baz'));
        $this->assertEquals(11, cstr::length('foo bar baz', 'UTF-8'));
    }

    public function testRandom() {
        $this->assertEquals(16, strlen(cstr::random()));
        $randomInteger = random_int(1, 100);
        $this->assertEquals($randomInteger, strlen(cstr::random($randomInteger)));
        $this->assertIsString(cstr::random());
    }

    public function testReplace() {
        $this->assertSame('foo bar laravel', cstr::replace('baz', 'laravel', 'foo bar baz'));
        $this->assertSame('foo bar baz 8.x', cstr::replace('?', '8.x', 'foo bar baz ?'));
        $this->assertSame('foo/bar/baz', cstr::replace(' ', '/', 'foo bar baz'));
        $this->assertSame('foo bar baz', cstr::replace(['?1', '?2', '?3'], ['foo', 'bar', 'baz'], '?1 ?2 ?3'));
    }

    public function testReplaceArray() {
        $this->assertSame('foo/bar/baz', cstr::replaceArray('?', ['foo', 'bar', 'baz'], '?/?/?'));
        $this->assertSame('foo/bar/baz/?', cstr::replaceArray('?', ['foo', 'bar', 'baz'], '?/?/?/?'));
        $this->assertSame('foo/bar', cstr::replaceArray('?', ['foo', 'bar', 'baz'], '?/?'));
        $this->assertSame('?/?/?', cstr::replaceArray('x', ['foo', 'bar', 'baz'], '?/?/?'));
        // Ensure recursive replacements are avoided
        $this->assertSame('foo?/bar/baz', cstr::replaceArray('?', ['foo?', 'bar', 'baz'], '?/?/?'));
        // Test for associative array support
        $this->assertSame('foo/bar', cstr::replaceArray('?', [1 => 'foo', 2 => 'bar'], '?/?'));
        $this->assertSame('foo/bar', cstr::replaceArray('?', ['x' => 'foo', 'y' => 'bar'], '?/?'));
    }

    public function testReplaceFirst() {
        $this->assertSame('fooqux foobar', cstr::replaceFirst('bar', 'qux', 'foobar foobar'));
        $this->assertSame('foo/qux? foo/bar?', cstr::replaceFirst('bar?', 'qux?', 'foo/bar? foo/bar?'));
        $this->assertSame('foo foobar', cstr::replaceFirst('bar', '', 'foobar foobar'));
        $this->assertSame('foobar foobar', cstr::replaceFirst('xxx', 'yyy', 'foobar foobar'));
        $this->assertSame('foobar foobar', cstr::replaceFirst('', 'yyy', 'foobar foobar'));
        // Test for multibyte string support
        $this->assertSame('Jxxxnköping Malmö', cstr::replaceFirst('ö', 'xxx', 'Jönköping Malmö'));
        $this->assertSame('Jönköping Malmö', cstr::replaceFirst('', 'yyy', 'Jönköping Malmö'));
    }

    public function testReplaceLast() {
        $this->assertSame('foobar fooqux', cstr::replaceLast('bar', 'qux', 'foobar foobar'));
        $this->assertSame('foo/bar? foo/qux?', cstr::replaceLast('bar?', 'qux?', 'foo/bar? foo/bar?'));
        $this->assertSame('foobar foo', cstr::replaceLast('bar', '', 'foobar foobar'));
        $this->assertSame('foobar foobar', cstr::replaceLast('xxx', 'yyy', 'foobar foobar'));
        $this->assertSame('foobar foobar', cstr::replaceLast('', 'yyy', 'foobar foobar'));
        // Test for multibyte string support
        $this->assertSame('Malmö Jönkxxxping', cstr::replaceLast('ö', 'xxx', 'Malmö Jönköping'));
        $this->assertSame('Malmö Jönköping', cstr::replaceLast('', 'yyy', 'Malmö Jönköping'));
    }

    public function testRemove() {
        $this->assertSame('Fbar', cstr::remove('o', 'Foobar'));
        $this->assertSame('Foo', cstr::remove('bar', 'Foobar'));
        $this->assertSame('oobar', cstr::remove('F', 'Foobar'));
        $this->assertSame('Foobar', cstr::remove('f', 'Foobar'));
        $this->assertSame('oobar', cstr::remove('f', 'Foobar', false));

        $this->assertSame('Fbr', cstr::remove(['o', 'a'], 'Foobar'));
        $this->assertSame('Fooar', cstr::remove(['f', 'b'], 'Foobar'));
        $this->assertSame('ooar', cstr::remove(['f', 'b'], 'Foobar', false));
        $this->assertSame('Foobar', cstr::remove(['f', '|'], 'Foo|bar'));
    }

    public function testSnake() {
        $this->assertSame('laravel_p_h_p_framework', cstr::snake('LaravelPHPFramework'));
        $this->assertSame('laravel_php_framework', cstr::snake('LaravelPhpFramework'));
        $this->assertSame('laravel php framework', cstr::snake('LaravelPhpFramework', ' '));
        $this->assertSame('laravel_php_framework', cstr::snake('Laravel Php Framework'));
        $this->assertSame('laravel_php_framework', cstr::snake('Laravel    Php      Framework   '));
        // ensure cache keys don't overlap
        $this->assertSame('laravel__php__framework', cstr::snake('LaravelPhpFramework', '__'));
        $this->assertSame('laravel_php_framework_', cstr::snake('LaravelPhpFramework_', '_'));
        $this->assertSame('laravel_php_framework', cstr::snake('laravel php Framework'));
        $this->assertSame('laravel_php_frame_work', cstr::snake('laravel php FrameWork'));
        // prevent breaking changes
        $this->assertSame('foo-bar', cstr::snake('foo-bar'));
        $this->assertSame('foo-_bar', cstr::snake('Foo-Bar'));
        $this->assertSame('foo__bar', cstr::snake('Foo_Bar'));
        $this->assertSame('żółtałódka', cstr::snake('ŻółtaŁódka'));
    }

    public function testStudly() {
        $this->assertSame('LaravelPHPFramework', cstr::studly('laravel_p_h_p_framework'));
        $this->assertSame('LaravelPhpFramework', cstr::studly('laravel_php_framework'));
        $this->assertSame('LaravelPhPFramework', cstr::studly('laravel-phP-framework'));
        $this->assertSame('LaravelPhpFramework', cstr::studly('laravel  -_-  php   -_-   framework   '));

        $this->assertSame('FooBar', cstr::studly('fooBar'));
        $this->assertSame('FooBar', cstr::studly('foo_bar'));
        $this->assertSame('FooBar', cstr::studly('foo_bar')); // test cache
        $this->assertSame('FooBarBaz', cstr::studly('foo-barBaz'));
        $this->assertSame('FooBarBaz', cstr::studly('foo-bar_baz'));
    }

    public function testMask() {
        $this->assertSame('tay*************', cstr::mask('taylor@email.com', '*', 3));
        $this->assertSame('******@email.com', cstr::mask('taylor@email.com', '*', 0, 6));
        $this->assertSame('tay*************', cstr::mask('taylor@email.com', '*', -13));
        $this->assertSame('tay***@email.com', cstr::mask('taylor@email.com', '*', -13, 3));

        $this->assertSame('****************', cstr::mask('taylor@email.com', '*', -17));
        $this->assertSame('*****r@email.com', cstr::mask('taylor@email.com', '*', -99, 5));

        $this->assertSame('taylor@email.com', cstr::mask('taylor@email.com', '*', 16));
        $this->assertSame('taylor@email.com', cstr::mask('taylor@email.com', '*', 16, 99));

        $this->assertSame('taylor@email.com', cstr::mask('taylor@email.com', '', 3));

        $this->assertSame('taysssssssssssss', cstr::mask('taylor@email.com', 'something', 3));
        $this->assertSame('taysssssssssssss', cstr::mask('taylor@email.com', cstr::of('something'), 3));

        $this->assertSame('这是一***', cstr::mask('这是一段中文', '*', 3));
        $this->assertSame('**一段中文', cstr::mask('这是一段中文', '*', 0, 2));
    }

    public function testMatch() {
        $this->assertSame('bar', cstr::match('/bar/', 'foo bar'));
        $this->assertSame('bar', cstr::match('/foo (.*)/', 'foo bar'));
        $this->assertEmpty(cstr::match('/nothing/', 'foo bar'));

        $this->assertEquals(['bar', 'bar'], cstr::matchAll('/bar/', 'bar foo bar')->all());

        $this->assertEquals(['un', 'ly'], cstr::matchAll('/f(\w*)/', 'bar fun bar fly')->all());
        $this->assertEmpty(cstr::matchAll('/nothing/', 'bar fun bar fly'));
    }

    public function testCamel() {
        $this->assertSame('laravelPHPFramework', cstr::camel('Laravel_p_h_p_framework'));
        $this->assertSame('laravelPhpFramework', cstr::camel('Laravel_php_framework'));
        $this->assertSame('laravelPhPFramework', cstr::camel('Laravel-phP-framework'));
        $this->assertSame('laravelPhpFramework', cstr::camel('Laravel  -_-  php   -_-   framework   '));

        $this->assertSame('fooBar', cstr::camel('FooBar'));
        $this->assertSame('fooBar', cstr::camel('foo_bar'));
        $this->assertSame('fooBar', cstr::camel('foo_bar')); // test cache
        $this->assertSame('fooBarBaz', cstr::camel('Foo-barBaz'));
        $this->assertSame('fooBarBaz', cstr::camel('foo-bar_baz'));
    }

    public function testSubstr() {
        $this->assertSame('Ё', cstr::substr('БГДЖИЛЁ', -1));
        $this->assertSame('ЛЁ', cstr::substr('БГДЖИЛЁ', -2));
        $this->assertSame('И', cstr::substr('БГДЖИЛЁ', -3, 1));
        $this->assertSame('ДЖИЛ', cstr::substr('БГДЖИЛЁ', 2, -1));
        $this->assertEmpty(cstr::substr('БГДЖИЛЁ', 4, -4));
        $this->assertSame('ИЛ', cstr::substr('БГДЖИЛЁ', -3, -1));
        $this->assertSame('ГДЖИЛЁ', cstr::substr('БГДЖИЛЁ', 1));
        $this->assertSame('ГДЖ', cstr::substr('БГДЖИЛЁ', 1, 3));
        $this->assertSame('БГДЖ', cstr::substr('БГДЖИЛЁ', 0, 4));
        $this->assertSame('Ё', cstr::substr('БГДЖИЛЁ', -1, 1));
        $this->assertEmpty(cstr::substr('Б', 2));
    }

    public function testSubstrCount() {
        $this->assertSame(3, cstr::substrCount('laravelPHPFramework', 'a'));
        $this->assertSame(0, cstr::substrCount('laravelPHPFramework', 'z'));
        $this->assertSame(1, cstr::substrCount('laravelPHPFramework', 'l', 2));
        $this->assertSame(0, cstr::substrCount('laravelPHPFramework', 'z', 2));
        $this->assertSame(1, cstr::substrCount('laravelPHPFramework', 'k', -1));
        $this->assertSame(1, cstr::substrCount('laravelPHPFramework', 'k', -1));
        $this->assertSame(1, cstr::substrCount('laravelPHPFramework', 'a', 1, 2));
        $this->assertSame(1, cstr::substrCount('laravelPHPFramework', 'a', 1, 2));
        $this->assertSame(3, cstr::substrCount('laravelPHPFramework', 'a', 1, -2));
        $this->assertSame(1, cstr::substrCount('laravelPHPFramework', 'a', -10, -3));
    }

    public function testUcfirst() {
        $this->assertSame('Laravel', cstr::ucfirst('laravel'));
        $this->assertSame('Laravel framework', cstr::ucfirst('laravel framework'));
        $this->assertSame('Мама', cstr::ucfirst('мама'));
        $this->assertSame('Мама мыла раму', cstr::ucfirst('мама мыла раму'));
    }

    public function testUuid() {
        $this->assertInstanceOf(UuidInterface::class, cstr::uuid());
        $this->assertInstanceOf(UuidInterface::class, cstr::orderedUuid());
    }

    public function testAsciiNull() {
        $this->assertSame('', cstr::ascii(null));
        $this->assertTrue(cstr::isAscii(null));
        $this->assertSame('', cstr::slug(null));
    }

    public function testPadBoth() {
        $this->assertSame('__Alien___', cstr::padBoth('Alien', 10, '_'));
        $this->assertSame('  Alien   ', cstr::padBoth('Alien', 10));
    }

    public function testPadLeft() {
        $this->assertSame('-=-=-Alien', cstr::padLeft('Alien', 10, '-='));
        $this->assertSame('     Alien', cstr::padLeft('Alien', 10));
    }

    public function testPadRight() {
        $this->assertSame('Alien-----', cstr::padRight('Alien', 10, '-'));
        $this->assertSame('Alien     ', cstr::padRight('Alien', 10));
    }

    public function testWordCount() {
        $this->assertEquals(2, cstr::wordCount('Hello, world!'));
        $this->assertEquals(10, cstr::wordCount('Hi, this is my first contribution to the Laravel framework.'));
    }

    public function validUuidList() {
        return [
            ['a0a2a2d2-0b87-4a18-83f2-2529882be2de'],
            ['145a1e72-d11d-11e8-a8d5-f2801f1b9fd1'],
            ['00000000-0000-0000-0000-000000000000'],
            ['e60d3f48-95d7-4d8d-aad0-856f29a27da2'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-21e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-31e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-41e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-51e1-9b21-0800200c9a66'],
            ['FF6F8CB0-C57D-11E1-9B21-0800200C9A66'],
        ];
    }

    public function invalidUuidList() {
        return [
            ['not a valid uuid so we can test this'],
            ['zf6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            ['145a1e72-d11d-11e8-a8d5-f2801f1b9fd1' . PHP_EOL],
            ['145a1e72-d11d-11e8-a8d5-f2801f1b9fd1 '],
            [' 145a1e72-d11d-11e8-a8d5-f2801f1b9fd1'],
            ['145a1e72-d11d-11e8-a8d5-f2z01f1b9fd1'],
            ['3f6f8cb0-c57d-11e1-9b21-0800200c9a6'],
            ['af6f8cb-c57d-11e1-9b21-0800200c9a66'],
            ['af6f8cb0c57d11e19b210800200c9a66'],
            ['ff6f8cb0-c57da-51e1-9b21-0800200c9a66'],
        ];
    }

    public function testMarkdown() {
        $this->assertSame("<p><em>hello world</em></p>\n", cstr::markdown('*hello world*'));
        $this->assertSame("<h1>hello world</h1>\n", cstr::markdown('# hello world'));
    }

    public function testRepeat() {
        $this->assertSame('aaaaa', cstr::repeat('a', 5));
        $this->assertSame('', cstr::repeat('', 5));
    }
}
// @codingStandardsIgnoreStart
class StringableObjectStub {
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function __toString() {
        return $this->value;
    }
}
