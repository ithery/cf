<?php

/**
 * @method mixed getAvailable()
 * @method mixed getError()
 * @method mixed getErrorType()
 * @method mixed getUsername()
 * @method bool isAvailable()
 * @method bool isError()
 * @method bool isErrorType()
 * @method bool isUsername()
 * @method setAvailable(mixed $value)
 * @method setError(mixed $value)
 * @method setErrorType(mixed $value)
 * @method setUsername(mixed $value)
 */
class InstagramAPI_Response_CheckUsernameResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $username;
    public $available;
    public $error;
    public $error_type;

}
