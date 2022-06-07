<?php

trait CApi_OAuth_Trait_ConvertPsrResponseTrait {
    /**
     * Convert a PSR7 response to a Illuminate Response.
     *
     * @param \Psr\Http\Message\ResponseInterface $psrResponse
     *
     * @return \CHTTP_Response
     */
    public function convertResponse($psrResponse) {
        return new CHTTP_Response(
            $psrResponse->getBody(),
            $psrResponse->getStatusCode(),
            $psrResponse->getHeaders()
        );
    }
}
