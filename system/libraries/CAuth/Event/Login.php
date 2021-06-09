<?php

class CAuth_Event_Login {
    use CQueue_Trait_SerializesModels;

    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The authenticated user.
     *
     * @var CAuth_AuthenticatableInterface
     */
    public $user;

    /**
     * Indicates if the user should be "remembered".
     *
     * @var bool
     */
    public $remember;

    /**
     * Create a new event instance.
     *
     * @param string                         $guard
     * @param CAuth_AuthenticatableInterface $user
     * @param bool                           $remember
     *
     * @return void
     */
    public function __construct($guard, $user, $remember) {
        $this->user = $user;
        $this->guard = $guard;
        $this->remember = $remember;
    }
}
