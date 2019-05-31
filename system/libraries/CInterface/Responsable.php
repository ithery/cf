<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 1, 2019, 11:39:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CInterface_Responsable {

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  CHTTP_Request  $request
     * @return CHTTP_Response
     */
    public function toResponse($request);
}
