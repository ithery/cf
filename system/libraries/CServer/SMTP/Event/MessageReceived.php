<?php

class CServer_SMTP_Event_MessageReceived {
    use CEvent_Trait_Dispatchable, CQueue_Trait_SerializesModels;

    /**
     * @var null|CAuth_AuthenticatableInterface
     */
    public $user;

    /**
     * @var CServer_SMTP_Message
     */
    public $message;

    /**
     * MessageReceived constructor.
     *
     * @param null|CAuth_AuthenticatableInterface $user
     * @param CServer_SMTP_Message                $message
     */
    public function __construct($user, CServer_SMTP_Message $message) {
        $this->user = $user;
        $this->message = $message;
    }
}
