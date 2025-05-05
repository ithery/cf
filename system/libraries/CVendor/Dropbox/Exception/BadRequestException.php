<?php


use Exception;
use Psr\Http\Message\ResponseInterface;

class CVendor_Dropbox_Exception_BadRequestException extends Exception
{
    public $dropboxCode = null;

    public $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $body = json_decode($response->getBody(), true);

        if ($body !== null) {
            if (isset($body['error']['.tag'])) {
                $this->dropboxCode = $body['error']['.tag'];
            }

            parent::__construct($body['error_summary']);
        }
    }
}
