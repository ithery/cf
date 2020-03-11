<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class COAuth_PersonalAccessTokenResult implements CInterface_Arrayable, CInterface_Jsonable {

    /**
     * The access token.
     *
     * @var string
     */
    public $accessToken;

    /**
     * The token model instance.
     *
     * @var COAuth_Model_Token
     */
    public $token;

    /**
     * Create a new result instance.
     *
     * @param  string  $accessToken
     * @param  COAuth_Model_Token  $token
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
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this->toArray(), $options);
    }

}
