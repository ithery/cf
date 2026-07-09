<?php

class CApi_Factory {
    /**
     * @param string              $className
     * @param string              $group
     * @param CApi_HTTP_Request   $request
     * @param array               $parameters
     *
     * @return CApi_MethodAbstract
     */
    public static function createMethod($className, $group, CApi_HTTP_Request $request, $parameters = []) {
        $method = new $className(CF::orgId(), $request->session()->getId(), $parameters);
        $method->setApiRequest($request)->setGroup($group);

        return $method;
    }
}
