<?php

class CAjax_Response implements CInterface_Responsable {
    /**
     * Create an HTTP response that represents the object.
     *
     * @param CHTTP_Request $request
     *
     * @return CHTTP_Response
     */
    public function toResponse($request) {
        if ($request->ajax()) {
        } else {
        }
    }
}
