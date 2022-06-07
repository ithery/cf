<?php

class CApi_OAuth_Event_AccessTokenCreated {
    /**
     * The newly created token ID.
     *
     * @var string
     */
    public $tokenId;

    /**
     * The ID of the user associated with the token.
     *
     * @var string
     */
    public $userId;

    /**
     * The ID of the client associated with the token.
     *
     * @var string
     */
    public $clientId;

    /**
     * The model of access token.
     *
     * @var CApi_OAuth_Model_OAuthAccessToken
     */
    public $accessTokenModel;

    /**
     * Create a new event instance.
     *
     * @param string                            $tokenId
     * @param null|string|int                   $userId
     * @param string                            $clientId
     * @param CApi_OAuth_Model_OAuthAccessToken $accessTokenModel
     *
     * @return void
     */
    public function __construct($tokenId, $userId, $clientId, $accessTokenModel) {
        $this->userId = $userId;
        $this->tokenId = $tokenId;
        $this->clientId = $clientId;
        $this->accessTokenModel = $accessTokenModel;
    }
}
