<?php

class CBot_Driver_NullDriver extends CBot_DriverAbstract {
    /**
     * @param CHTTP_Request $request
     */
    public function buildPayload(CHTTP_Request $request) {
    }

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest() {
        return true;
    }

    /**
     * Return the driver name.
     *
     * @return string
     */
    public static function getName() {
        return '';
    }

    /**
     * @param CBot_Message_Incoming_IncomingMessage $message
     *
     * @return Answer
     */
    public function getConversationAnswer(CBot_Message_Incoming_IncomingMessage $message) {
        return CBot_Message_Incoming_Answer::create('')->setMessage($message);
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages() {
        return [new CBot_Message_Incoming_IncomingMessage('', '', '')];
    }

    /**
     * @return bool
     */
    public function isBot() {
        return false;
    }

    /**
     * @param string|\CBot_Message_Outgoing_Question $message
     * @param CBot_Message_Incoming_IncomingMessage  $matchingMessage
     * @param array                                  $additionalParameters
     *
     * @return $this
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = []) {
    }

    /**
     * @param mixed $payload
     *
     * @return CHTTP_Response
     */
    public function sendPayload($payload) {
    }

    /**
     * @return bool
     */
    public function hasMatchingEvent() {
        return false;
    }

    /**
     * @return bool
     */
    public function isConfigured() {
        return false;
    }

    /**
     * Retrieve User information.
     *
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return User
     */
    public function getUser(CBot_Message_Incoming_IncomingMessage $matchingMessage) {
        return new CBot_User();
    }

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param string                                $endpoint
     * @param array                                 $parameters
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return void
     */
    public function sendRequest($endpoint, array $parameters, CBot_Message_Incoming_IncomingMessage $matchingMessage) {
    }
}
