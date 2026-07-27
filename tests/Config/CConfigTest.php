<?php
use PHPUnit\Framework\TestCase;

/**
 * `CConfig::instance($group)` is a thin, group-prefixing facade in front of
 * the single global `CConfig_Manager::instance()->repository()` that the
 * whole framework (and Bootstrap.php's own config loading) shares - so
 * every test here uses its own uniquely-named group to avoid colliding
 * with or leaking into real app config groups loaded elsewhere.
 */
class CConfigTest extends TestCase {
    protected function group() {
        return 'cconfigtest_' . uniqid();
    }

    public function testInstanceReturnsTheSameObjectForTheSameGroup() {
        $group = $this->group();

        $this->assertSame(CConfig::instance($group), CConfig::instance($group));
    }

    public function testInstanceReturnsDifferentObjectsForDifferentGroups() {
        $this->assertNotSame(CConfig::instance($this->group()), CConfig::instance($this->group()));
    }

    public function testInstanceRejectsANonStringGroup() {
        $this->expectException(Exception::class);

        CConfig::instance(['not', 'a', 'string']);
    }

    public function testSetAndGetPrefixesTheKeyWithTheGroupName() {
        $group = $this->group();
        $config = CConfig::instance($group);

        $config->set('foo', 'bar');

        $this->assertSame('bar', $config->get('foo'));
        $this->assertSame('bar', CConfig::repository()->get($group . '.foo'));
    }

    public function testGetWithoutAKeyReturnsTheWholeGroup() {
        $group = $this->group();
        $config = CConfig::instance($group);

        $config->set('foo', 'bar');
        $config->set('baz', 'qux');

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $config->get());
        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $config->all());
        $this->assertSame($config->all(), $config->toArray());
    }

    public function testGetReturnsDefaultForMissingKey() {
        $config = CConfig::instance($this->group());

        $this->assertSame('fallback', $config->get('missing', 'fallback'));
    }

    public function testGetManySupportsListAndKeyedDefaults() {
        $config = CConfig::instance($this->group());
        $config->set('foo', 'bar');

        $result = $config->getMany([
            'foo',
            'missing' => 'fallback',
        ]);

        $this->assertSame(['foo' => 'bar', 'missing' => 'fallback'], $result);
    }

    public function testHasReflectsWhetherTheKeyWasSet() {
        $config = CConfig::instance($this->group());

        $this->assertFalse($config->has('foo'));

        $config->set('foo', 'bar');

        $this->assertTrue($config->has('foo'));
    }

    public function testPrependAddsToTheStartOfAnArrayValue() {
        $config = CConfig::instance($this->group());
        $config->set('list', ['b', 'c']);

        $config->prepend('list', 'a');

        $this->assertSame(['a', 'b', 'c'], $config->get('list'));
    }

    public function testPushAddsToTheEndOfAnArrayValue() {
        $config = CConfig::instance($this->group());
        $config->set('list', ['a', 'b']);

        $config->push('list', 'c');

        $this->assertSame(['a', 'b', 'c'], $config->get('list'));
    }

    public function testArrayAccessDelegatesToGetSetHasAndUnsetThroughTheGroupPrefix() {
        // Regression coverage for a real bug fixed 2026-07-27: has() (and by
        // extension offsetExists()) used to forward the bare key straight to
        // repository()->has() with no group prefix, unlike get()/set(), so
        // isset() right after a set() was always false. See TODO.md history.
        $config = CConfig::instance($this->group());

        $this->assertFalse(isset($config['foo']));

        $config['foo'] = 'bar';

        $this->assertTrue(isset($config['foo']));
        $this->assertSame('bar', $config['foo']);

        // offsetUnset() only nulls the value out (set($key, null)), it doesn't
        // remove the key from the repository, so has()/isset() stays true -
        // only the value itself goes away.
        unset($config['foo']);
        $this->assertNull($config['foo']);
    }

    public function testSetAcceptsAnArrayOfKeyValuePairs() {
        $config = CConfig::instance($this->group());

        $config->set(['foo' => 'bar', 'baz' => 'qux'], null);

        $this->assertSame('bar', $config->get('foo'));
        $this->assertSame('qux', $config->get('baz'));
    }
}
