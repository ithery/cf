<?php

interface CBot_Contract_DriverInterface {
    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest();

    /**
     * Retrieve the chat message(s).
     *
     * @return array
     */
    public function getMessages();

    /**
     * @return bool
     */
    public function isConfigured();

    /**
     * Retrieve User information.
     *
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return CBot_Contract_UserInterface
     */
    public function getUser(CBot_Message_Incoming_IncomingMessage $matchingMessage);

    /**
     * @param CBot_Message_Incoming_IncomingMessage $message
     *
     * @return \CBot_Message_Incoming_Answer
     */
    public function getConversationAnswer(CBot_Message_Incoming_IncomingMessage $message);

    /**
     * @param string|\CBot_Message_Outgoing_Question $message
     * @param CBot_Message_Incoming_IncomingMessage  $matchingMessage
     * @param array                                  $additionalParameters
     *
     * @return $this
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = []);

    /**
     * @param mixed $payload
     *
     * @return CHTTP_Response
     */
    public function sendPayload($payload);

    /**
     * Return the driver name.
     *
     * @return string
     */
    public static function getName();

    /**
     * Does the driver match to an incoming messaging service event.
     *
     * @return bool|mixed
     */
    public function hasMatchingEvent();

    /**
     * Send a typing indicator.
     *
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return mixed
     */
    public function types(CBot_Message_Incoming_IncomingMessage $matchingMessage);

    /**
     * Send a typing indicator and wait for the given amount of seconds.
     *
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     * @param float                                 $seconds
     *
     * @return mixed
     */
    public function typesAndWaits(CBot_Message_Incoming_IncomingMessage $matchingMessage, $seconds);

    /**
     * Tells if the stored conversation callbacks are serialized.
     *
     * @return bool
     */
    public function serializesCallbacks();
}
