<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CInterface_Responsable {
    /**
     * Create an HTTP response that represents the object.
     *
     * @param CHTTP_Request $request
     *
     * @return CHTTP_Response
     */
    public function toResponse($request);
}
