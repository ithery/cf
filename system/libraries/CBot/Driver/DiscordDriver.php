<?php

use Discord\Discord;
use Discord\Parts\Channel\Message;
use React\Promise\PromiseInterface;

class CBot_Driver_DiscordDriver implements CBot_Contract_DriverInterface {
    const DRIVER_NAME = 'Discord';

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var Discord
     */
    protected $client;

    /**
     * @var string
     */
    protected $bot_id;

    protected $file;

    /**
     * Driver constructor.
     *
     * @param array   $config
     * @param Discord $client
     */
    public function __construct(array $config, Discord $client) {
        $this->event = CCollection::make();
        $this->config = CCollection::make($config);
        $this->client = $client;

        $this->client->on('message', function (Message $message) {
            $this->message = $message;
        });
    }

    /**
     * Connected event.
     */
    public function connected() {
    }

    /**
     * Return the driver name.
     *
     * @return string
     */
    public static function getName() {
        return self::DRIVER_NAME;
    }

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest() {
        return false;
    }

    /**
     * @return bool|CBot_Contract_DriverEventInterface
     */
    public function hasMatchingEvent() {
        return false;
    }

    /**
     * @param CBot_Message_Incoming_IncomingMessage $message
     *
     * @return CBot_Message_Incoming_Answer
     */
    public function getConversationAnswer(CBot_Message_Incoming_IncomingMessage $message) {
        return CBot_Message_Incoming_Answer::create($this->message->content)->setMessage($message);
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages() {
        $messageText = $this->message->content;
        $user_id = $this->message->author->id;
        $channel_id = $this->message->channel->id;

        $message = new CBot_Message_Incoming_IncomingMessage($messageText, $user_id, $channel_id, $this->message);
        $message->setIsFromBot($this->isBot());

        return [$message];
    }

    /**
     * @return bool
     */
    protected function isBot() {
        return false;
    }

    /**
     * @param string|\CBot_Message_Outgoing_Question|CBot_Message_Incoming_IncomingMessage $message
     * @param CBot_Message_Incoming_IncomingMessage                                        $matchingMessage
     * @param array                                                                        $additionalParameters
     *
     * @return mixed
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = []) {
        $payload = [
            'message' => '',
            'embed' => '',
        ];

        if ($message instanceof CBot_Message_Outgoing_OutgoingMessage) {
            $payload['message'] = $message->getText();

            $attachment = $message->getAttachment();

            if (!is_null($attachment)) {
                if ($attachment instanceof CBot_Message_Attachment_Image) {
                    $payload['embed'] = [
                        'image' => [
                            'url' => $attachment->getUrl(),
                        ],
                    ];
                }
            }
        } else {
            $payload['message'] = $message;
        }

        return $payload;
    }

    /**
     * @param mixed $payload
     *
     * @return PromiseInterface
     */
    public function sendPayload($payload) {
        return $this->message->channel->sendMessage($payload['message'], false, $payload['embed']);
    }

    /**
     * @return bool
     */
    public function isConfigured() {
        return !is_null($this->config->get('token'));
    }

    /**
     * Send a typing indicator.
     *
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return mixed
     */
    public function types(CBot_Message_Incoming_IncomingMessage $matchingMessage) {
    }

    /**
     * Send a typing indicator and wait for the given amount of seconds.
     *
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     * @param float                                 $seconds
     *
     * @return mixed
     */
    public function typesAndWaits(CBot_Message_Incoming_IncomingMessage $matchingMessage, $seconds) {
    }

    /**
     * Retrieve User information.
     *
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return CBot_User
     */
    public function getUser(CBot_Message_Incoming_IncomingMessage $matchingMessage) {
        $user = null;
        $this->client->getUserById($matchingMessage->getSender())->then(function ($_user) use (&$user) {
            $user = $_user;
        });
        if (!is_null($user)) {
            return new CBot_User(
                $matchingMessage->getSender(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getUsername()
            );
        }

        return new CBot_User($this->message->author->id, '', '', $this->message->author->username);
    }

    /**
     * @return Discord
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param $endpoint
     * @param array                                 $parameters
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return \React\Promise\PromiseInterface
     */
    public function sendRequest($endpoint, array $parameters, CBot_Message_Incoming_IncomingMessage $matchingMessage) {
    }

    /**
     * Tells if the stored conversation callbacks are serialized.
     *
     * @return bool
     */
    public function serializesCallbacks() {
        return false;
    }
}
