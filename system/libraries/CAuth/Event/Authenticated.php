<?php

class CAuth_Event_Authenticated {
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
     * Create a new event instance.
     *
     * @param string                         $guard
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    public function __construct($guard, $user) {
        $this->user = $user;
        $this->guard = $guard;
    }
}
