<?php

/**
 * @method mixed getAvailable()
 * @method mixed getConfirmed()
 * @method mixed getErrorType()
 * @method string[] getUsernameSuggestions()
 * @method mixed getValid()
 * @method bool isAvailable()
 * @method bool isConfirmed()
 * @method bool isErrorType()
 * @method bool isUsernameSuggestions()
 * @method bool isValid()
 * @method setAvailable(mixed $value)
 * @method setConfirmed(mixed $value)
 * @method setErrorType(mixed $value)
 * @method setUsernameSuggestions(string[] $value)
 * @method setValid(mixed $value)
 */
class InstagramAPI_Response_CheckEmailResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $valid;
    public $available;
    public $confirmed;

    /**
     * @var string[]
     */
    public $username_suggestions;
    public $error_type;

}
