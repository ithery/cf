<?php

class CApi_OAuth_PersonalAccessTokenResult implements CInterface_Arrayable, CInterface_Jsonable {
    /**
     * The access token.
     *
     * @var string
     */
    public $accessToken;

    /**
     * The token model instance.
     *
     * @var \CApi_OAuth_Model_OAuthAccessToken
     */
    public $token;

    /**
     * Create a new result instance.
     *
     * @param string                             $accessToken
     * @param \CApi_OAuth_Model_OAuthAccessToken $token
     *
     * @return void
     */
    public function __construct($accessToken, $token) {
        $this->token = $token;
        $this->accessToken = $accessToken;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'accessToken' => $this->accessToken,
            'token' => $this->token,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this->toArray(), $options);
    }
}
