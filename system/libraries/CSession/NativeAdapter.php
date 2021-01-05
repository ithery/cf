<?php

/**
 * Description of NativeAdapter
 * will assigned to $_SESSION
 *
 * @author Hery
 */
class CSession_NativeAdapter implements \ArrayAccess {
    public function offsetExists($offset) {
        return c::session()->has($offset);
    }

    public function offsetGet($offset) {
        return c::session()->get($offset);
    }

    public function offsetSet($offset, $value) {
        return c::session()->put($offset, $value);
    }

    public function offsetUnset($offset) {
        return c::session()->forget($offset);
    }

    public function data() {
        return c::session()->all();
    }
}
