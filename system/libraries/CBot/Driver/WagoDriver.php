<?php
class CBot_Driver_WagoDriver extends CBot_DriverAbstract {
    const DRIVER_NAME = 'Wago';

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var CCollection
     */
    protected $files;

    /**
     * @param CHTTP_Request $request
     */
    public function buildPayload(CHTTP_Request $request) {
        $this->payload = $request->request->all();

        $this->event = CCollection::make($this->payload);
        $this->files = CCollection::make($request->files->all());
        $this->config = c::collect(carr::get($this->config->get('drivers', []), 'wago', []));
    }

    public static function getName() {
        return static::DRIVER_NAME;
    }

    /**
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return \CBot_Contract_UserInterface
     */
    public function getUser(CBot_Message_Incoming_IncomingMessage $matchingMessage) {
        return new CBot_User($matchingMessage->getSender());
    }

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest() {
        throw new Exception('matchesRequest:' . json_encode($this->event->toArray()));

        return $this->event->has('data')
            && $this->event->has('type')
            && $this->getEventData('type') == 'device:message:incoming'
            && $this->getEventData('data.message.ack') == '1'
            && $this->getEventData('data.message.fromMe') == false;
    }

    private function getEventData($key, $default = null) {
        return carr::get($this->event->toArray(), $key, $default);
    }

    /**
     * @return bool
     */
    public function isBot() {
        return false;
    }

    /**
     * @return bool
     */
    public function isConfigured() {
        return true;
    }

    /**
     * @return bool|DriverEventInterface
     */
    public function hasMatchingEvent() {
        return false;
    }

    /**
     * @param CBot_Message_Incoming_IncomingMessage $message
     *
     * @return \CBot_Message_Incoming_Answer
     */
    public function getConversationAnswer(CBot_Message_Incoming_IncomingMessage $message) {
        return CBot_Message_Incoming_Answer::create($message->getText())
            ->setValue($message->getText())
            ->setInteractiveReply(true)
            ->setMessage($message);
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages() {
        if (empty($this->messages)) {
            $sender = $this->getEventData('data.message.author');
            if (!$sender) {
                $this->getEventData('data.message.from');
            }
            $message = new CBot_Message_Incoming_IncomingMessage($this->getEventData('data.message.body'), $sender, $this->getEventData('data.message.to'), $this->event->toArray());

            $this->messages = [$message];
        }

        return $this->messages;
    }

    /**
     * @param string|CBot_Message_Outgoing_Question|CBot_Message_Outgoing_OutgoingMessage $message
     * @param CBot_Message_Incoming_IncomingMessage                                       $matchingMessage
     * @param array                                                                       $additionalParameters
     *
     * @return CHTTP_Response
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = []) {
        $parameters = $additionalParameters;
        $text = '';

        $parameters['originate'] = $matchingMessage->getRecipient() === '';
        $parameters['recipient'] = $matchingMessage->getSender();
        $parameters['sender'] = carr::get($matchingMessage->getPayload(), 'data.message.from');
        $parameters['buttons'] = [];

        if ($message instanceof CBot_Message_Outgoing_Question) {
            $text = $message->getText();
            $parameters['buttons'] = $message->getButtons() ?? [];
        } else {
            $text = $message;
        }

        $parameters['text'] = $text;

        return $parameters;
    }

    /**
     * @param mixed $payload
     *
     * @return CHTTP_Response
     */
    public function sendPayload($payload) {
        $outgoing = carr::get($payload, 'text');
        /** @var CBot_Message_Outgoing_OutgoingMessage $outgoing */
        $sender = carr::get($payload, 'sender');

        $token = $this->config->get('token');
        $options = $this->config->get('options', []);
        $deviceApi = CVendor::wago()->device($token, $options);

        return $deviceApi->sendMessage($sender, $outgoing->getText());
        //throw new Exception('sendPayload' . (is_string($payload) ? $payload : json_encode($payload)));
    }

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param string                                 $endpoint
     * @param array                                  $parameters
     * @param \CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return void
     */
    public function sendRequest($endpoint, array $parameters, CBot_Message_Incoming_IncomingMessage $matchingMessage) {
        //throw new Exception('sendRequest:' . $endpoint);
    }
}
