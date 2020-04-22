<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CSocialLogin_OAuth2_Provider_SignInWithAppleProvider extends CSocialLogin_OAuth2_AbstractProvider implements CSocialLogin_OAuth2_ProviderInterface {

    protected $encodingType = PHP_QUERY_RFC3986;
    protected $scopeSeparator = " ";

    protected function getAuthUrl($state) {
        return $this->buildAuthUrlFromBase(
                        'https://appleid.apple.com/auth/authorize', $state
        );
    }

    protected function getCodeFields($state = null) {
        $fields = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            'response_type' => 'code',
            'response_mode' => 'form_post',
        ];

        if ($this->usesState()) {
            $fields['state'] = $state;
        }

        return array_merge($fields, $this->parameters);
    }

    protected function getTokenUrl() {
        return "https://appleid.apple.com/auth/token";
    }

    public function getAccessToken($code) {
        $response = $this->getHttpClient()
                ->post(
                $this->getTokenUrl(), [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(
                        $this->clientId . ':' . $this->clientSecret
                ),
            ],
            'body' => $this->getTokenFields($code),
                ]
        );

        return $this->parseAccessToken($response->getBody());
    }

    protected function parseAccessToken($response) {
        $data = $response->json();

        return $data['access_token'];
    }

    protected function getTokenFields($code) {
        $fields = parent::getTokenFields($code);
        $fields["grant_type"] = "authorization_code";

        return $fields;
    }

    protected function getUserByToken($token) {
        $claims = explode('.', $token)[1];

        return json_decode(base64_decode($claims), true);
    }

    public function user() {
        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->mapUserToObject($this->getUserByToken(
                        carr::get($response, 'id_token')
        ));

        return $user
                        ->setToken(carr::get($response, 'access_token'))
                        ->setRefreshToken(carr::get($response, 'refresh_token'))
                        ->setExpiresIn(carr::get($response, 'expires_in'));
    }

    protected function mapUserToObject(array $user) {
        $fullName = null;
        $getRequest = array_merge($_GET,$_POST);
        if (isset($getRequest['user'])) {
            $userRequest = json_decode($getRequest['user'], true);

            if (array_key_exists("name", $userRequest)) {
                $user["name"] = $userRequest["name"];
                $fullName = trim(carr::get($user, 'name.firstName', '') . " " . carr::get($user, 'name.lastName', ''));
            }
        }

        return (new CSocialLogin_OAuth2_User)
                        ->setRaw($user)
                        ->map([
                            "id" => $user["sub"],
                            "name" => strlen($fullName) > 0 ? $fullName : null,
                            "email" => carr::get($user, "email"),
        ]);
    }

}
