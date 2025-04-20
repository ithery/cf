<?php

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class CModel_AccessToken_NewAccessToken implements Arrayable, Jsonable {
    /**
     * The access token instance.
     *
     * @var \CModel_AccessToken_AccessTokenModel
     */
    public $accessToken;

    /**
     * The plain text version of the token.
     *
     * @var string
     */
    public $plainTextToken;

    /**
     * Create a new access token result.
     *
     * @param \CModel_AccessToken_AccessTokenModel $accessToken
     * @param string                               $plainTextToken
     *
     * @return void
     */
    public function __construct(CModel_AccessToken_AccessTokenModel $accessToken, $plainTextToken) {
        $this->accessToken = $accessToken;
        $this->plainTextToken = $plainTextToken;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'accessToken' => $this->accessToken,
            'plainTextToken' => $this->plainTextToken,
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
