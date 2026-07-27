<?php
use PHPUnit\Framework\TestCase;

/**
 * Covers the try/catch protection added directly to the vendored
 * `Opis\Closure\SerializableClosure`/`ReflectionClosure` (system/vendor/Opis/Closure/)
 * for the same "Failed to retrieve the reflection object" PHP reflection
 * engine edge case already fixed in `CFunction_SerializableClosure`'s own
 * NativeSerializer (see tests/Function/SerializableClosureTest.php).
 *
 * The vendored library itself was patched (rather than migrating every
 * remaining `Opis\Closure\SerializableClosure` call site) because some
 * consumers - notably `CQueue_SerializableClosure`, used to serialize
 * closures into already-queued jobs - extend Opis's class directly via
 * inheritance hooks (transformUseVariables()/resolveUseVariables()) that
 * don't have a drop-in equivalent in CFunction_SerializableClosure's
 * composition-based design, and rewriting them risks breaking
 * already-persisted queue payloads.
 */
class OpisSerializableClosureTest extends TestCase {
    public function testBasicClosureRoundTrips() {
        $wrapped = new \Opis\Closure\SerializableClosure(function ($x) {
            return $x * 2;
        });

        $restored = unserialize(serialize($wrapped));

        $this->assertSame(8, $restored(4));
    }

    /**
     * Regression test mirroring SerializableClosureTest::
     * testNativeSerializerFallsBackToNullScopeWhenReflectionCannotDetermineClosureScope,
     * but against the vendored Opis classes directly.
     */
    public function testSerializeFallsBackToNullScopeWhenReflectionCannotDetermineClosureScope() {
        $closure = function ($x) {
            return $x + 1;
        };
        $serializable = new OpisSerializableClosureTest_FailingScopeReflectorClosure($closure);

        // Opis's SerializableClosure implements the legacy Serializable
        // interface (serialize()/unserialize()), not __serialize()/__unserialize().
        $serialized = $serializable->serialize();
        $this->assertStringContainsString('s:5:"scope";N;', $serialized);

        $restored = unserialize(serialize($serializable));
        $this->assertSame(5, $restored(4));
    }

    public function testGetCodeFallsBackWhenReflectionCannotDetermineClosureScope() {
        $closure = function ($x) {
            return $x + 1;
        };
        $reflector = new OpisSerializableClosureTest_ThrowingScopeReflectionClosure($closure);

        // getCode() calls getClosureScopeClass() itself (to resolve the
        // __CLASS__ context for self::/static::/parent:: rewriting) - it
        // must not crash even though this reflector always throws.
        $code = $reflector->getCode();

        $this->assertStringContainsString('function ($x)', $code);
    }
}

class OpisSerializableClosureTest_ThrowingScopeReflectionClosure extends \Opis\Closure\ReflectionClosure {
    public function getClosureScopeClass() {
        throw new Error('Failed to retrieve the reflection object');
    }
}

class OpisSerializableClosureTest_FailingScopeReflectorClosure extends \Opis\Closure\SerializableClosure {
    public function getReflector() {
        if ($this->reflector === null) {
            $this->reflector = new OpisSerializableClosureTest_ThrowingScopeReflectionClosure($this->getClosure());
        }

        return $this->reflector;
    }
}
