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
        // Unlike get()/set(), has() does not prefix the key with the group
        // name (see the dedicated bug-documentation test below) - so the
        // caller must pass the fully-qualified "group.key" here themselves.
        $group = $this->group();
        $config = CConfig::instance($group);

        $this->assertFalse($config->has($group . '.foo'));

        $config->set('foo', 'bar');

        $this->assertTrue($config->has($group . '.foo'));
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

    public function testArrayAccessGetAndUnsetDelegateThroughTheGroupPrefix() {
        $config = CConfig::instance($this->group());

        $config['foo'] = 'bar';
        $this->assertSame('bar', $config['foo']);

        unset($config['foo']);
        $this->assertNull($config['foo']);
    }

    /**
     * Documents a real inconsistency in CConfig rather than silently
     * asserting it away: get()/set()/offsetGet()/offsetSet() all prefix the
     * key with "$this->group." before touching the repository, but has()
     * (system/libraries/CConfig.php:144-146) forwards the bare key straight
     * to repository()->has() with no group prefix. So offsetExists()
     * ("isset($config[...])") checks a completely different, unprefixed key
     * than offsetSet() just wrote - isset() on a key you just set() is
     * expected to be true and currently is not. Not fixed here since this
     * suite was scoped to test-writing, not behavior changes - see TODO.md.
     */
    public function testIssetIsInconsistentWithSetBecauseHasDoesNotPrefixTheGroup() {
        $config = CConfig::instance($this->group());

        $config['foo'] = 'bar';

        $this->assertFalse(isset($config['foo']), 'this documents the bug: expected isset() to be true here');
    }

    public function testSetAcceptsAnArrayOfKeyValuePairs() {
        $config = CConfig::instance($this->group());

        $config->set(['foo' => 'bar', 'baz' => 'qux'], null);

        $this->assertSame('bar', $config->get('foo'));
        $this->assertSame('qux', $config->get('baz'));
    }
}
