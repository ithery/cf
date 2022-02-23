<?php

class CApi_Factory {
    public static function createMethod($className, CApi_HTTP_Request $request, $parameters = []) {
        $method = new $className(CF::orgId(), $request->session()->getId(), $parameters);

        return $method;
    }
}
