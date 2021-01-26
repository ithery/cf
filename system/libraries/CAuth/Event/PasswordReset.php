<?php

class CAuth_Event_PasswordReset {
    use CQueue_Trait_SerializesModels;

    /**
     * The user.
     *
     * @var CAuth_AuthenticatableInterface
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    public function __construct($user) {
        $this->user = $user;
    }
}
