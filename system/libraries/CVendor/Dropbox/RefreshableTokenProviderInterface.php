<?php

use GuzzleHttp\Exception\ClientException;

interface CVendor_Dropbox_RefreshableTokenProviderInterface extends CVendor_Dropbox_TokenProviderInterface {
    /**
     * @return bool whether the token was refreshed
     */
    public function refresh(ClientException $exception): bool;
}
