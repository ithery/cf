<?php

/**
 * Description of CFHTTP
 *
 * @author Hery
 */
class CFHTTP {

    public static function execute() {
        $kernel = new CHTTP_Kernel();
        $response = $kernel->handle(
                $request = CHTTP_Request::capture()
        )->send();


        $kernel->terminate($request, $response);
    }

}
