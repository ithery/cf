<?php
class CApp_Auth_Response_LoginResponse implements CApp_Auth_Contact_LoginResponseInterface {
    /**
     * Create an HTTP response that represents the object.
     *
     * @param CHTTP_Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request) {
        return $request->wantsJson()
            ? c::response()->json(['two_factor' => false])
            : c::redirect()->intended(CF::config('auth.home'));
    }
}
