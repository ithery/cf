<?php

class CApi_Factory {
    public static function createMethod($className, $group, CApi_HTTP_Request $request, $parameters = []) {
        $method = new $className(CF::orgId(), $request->session()->getId(), $parameters);
        $method->setApiRequest($request)->setGroup($group);

        return $method;
    }
}
