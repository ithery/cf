<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:54:42 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Exception_PartialShardFailureException extends CElastic_Exception_ResponseException {

    /**
     * Construct Exception.
     *
     * @param CElastic_Client_Request  $request
     * @param CElastic_Client_Response $response
     */
    public function __construct(CElastic_Client_Request $request, CElastic_Client_Response $response) {
        parent::__construct($request, $response);
        $shardsStatistics = $response->getShardsStatistics();
        $this->message = JSON::stringify($shardsStatistics);
    }

}
