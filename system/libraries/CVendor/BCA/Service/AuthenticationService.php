<?php

class CVendor_BCA_Service_AuthenticationService extends CVendor_BCA_ServiceAbstract {
    /**
     * AccessToken.
     *
     * @return array
     */
    public function accessToken() {
        $requestUrl = '/api/oauth/token';

        return $this->sendRequest('POST', $requestUrl, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->api->getClientId() . ':' . $this->api->getClientSecret()),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);
    }
}
