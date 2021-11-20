<?php

class CBroadcast_BroadcastController extends CController {
    /**
     * Authenticate the request for channel access.
     *
     * @return \CHTTP_Response
     */
    public function authenticate() {
        $request = CHTTP::request();
        if ($request->hasSession()) {
            $request->session()->reflash();
        }

        return CBroadcast::manager()->auth($request);
    }
}
