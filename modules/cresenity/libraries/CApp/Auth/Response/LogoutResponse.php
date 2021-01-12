<?php
class CApp_Auth_Response_LogoutResponse implements CApp_Auth_Contract_LogoutResponseInterface {
    /**
     * Create an HTTP response that represents the object.
     *
     * @param CHTTP_Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request) {
        return $request->wantsJson()
            ? c::response()->json('', 204)
            : c::redirect()->intended(CF::config('auth.home'));
    }
}
