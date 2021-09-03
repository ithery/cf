<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 1, 2019, 11:39:59 PM
 */
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
