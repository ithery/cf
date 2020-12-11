<?php

/**
 * Description of NativeAdapter
 * will assigned to $_SESSION
 *
 * @author Hery
 */
class CSession_NativeAdapter implements \ArrayAccess {

    public function offsetExists($offset) {
        return Session::has($offset);
    }

    public function offsetGet($offset) {
        return Session::get($offset);
    }

    public function offsetSet($offset, $value) {
        return Session::put($offset, $value);
    }

    public function offsetUnset($offset) {
        return Session::forget($offset);
    }

}
