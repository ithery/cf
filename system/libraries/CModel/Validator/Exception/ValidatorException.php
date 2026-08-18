<?php

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class ValidatorException.
 */
class CModel_Validator_Exception_ValidatorException extends \Exception implements Jsonable, Arrayable {
    /**
     * @var CBase_MessageBag
     */
    protected $messageBag;

    /**
     * @param CBase_MessageBag $messageBag
     */
    public function __construct(CBase_MessageBag $messageBag) {
        $this->messageBag = $messageBag;
    }

    /**
     * @return MessageBag
     */
    public function getMessageBag() {
        return $this->messageBag;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'error' => 'validation_exception',
            'error_description' => $this->getMessageBag()
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
