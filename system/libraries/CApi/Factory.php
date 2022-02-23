<?php

class CApi_Factory {
    public static function createMethod($className, $request, $parameters = []) {
        $method = new $className(FB::orgId(), $request->session()->id(), $parameters);

        return $method;
    }
}
