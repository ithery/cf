<?php
class CBot_Driver_WebDriver extends CBot_DriverAbstract {
    const DRIVER_NAME = 'Web';

    const ATTACHMENT_IMAGE = 'image';

    const ATTACHMENT_AUDIO = 'audio';

    const ATTACHMENT_VIDEO = 'video';

    const ATTACHMENT_FILE = 'file';

    const ATTACHMENT_LOCATION = 'location';

    /**
     * @var CBot_Message_Outgoing_OutgoingMessage[]
     */
    protected $replies = [];

    /**
     * @var int
     */
    protected $replyStatusCode = 200;

    /**
     * @var string
     */
    protected $errorMessage = '';

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
        $this->config = CCollection::make($this->config->get('drivers.web', []));
    }

    public static function getName() {
        return static::DRIVER_NAME;
    }

    /**
     * Retrieve User information.
     *
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return CBot_Contract_UserInterface
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
        return CCollection::make($this->config->get('matchingData'))->diffAssoc($this->event)->isEmpty();
    }

    /**
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return void
     */
    public function types(CBot_Message_Incoming_IncomingMessage $matchingMessage) {
        $this->replies[] = [
            'message' => CBot_Driver_WebDriver_TypingIndicator::create(),
            'additionalParameters' => [],
        ];
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
        $this->replies[] = [
            'message' => CBot_Driver_WebDriver_TypingIndicator::create($seconds),
            'additionalParameters' => [],
        ];
    }

    /**
     * @param CBot_Message_Incoming_IncomingMessage $message
     *
     * @return \CBot_Message_Incoming_Answer
     */
    public function getConversationAnswer(CBot_Message_Incoming_IncomingMessage $message) {
        $interactive = $this->event->get('interactive', false);
        if (is_string($interactive)) {
            $interactive = ($interactive !== 'false') && ($interactive !== '0');
        } else {
            $interactive = (bool) $interactive;
        }

        return CBot_Message_Incoming_Answer::create($message->getText())
            ->setValue($this->event->get('value', $message->getText()))
            ->setMessage($message)
            ->setInteractiveReply($interactive);
    }

    /**
     * @return bool
     */
    public function hasMatchingEvent() {
        $event = false;

        if ($this->event->has('eventData')) {
            $event = new CBot_Event_GenericEvent($this->event->get('eventData'));
            $event->setName($this->event->get('eventName'));
        }

        return $event;
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages() {
        if (empty($this->messages)) {
            $message = $this->event->get('message');
            $userId = $this->event->get('userId');
            $sender = $this->event->get('sender', $userId);

            $incomingMessage = new CBot_Message_Incoming_IncomingMessage($message, $sender, $userId, $this->payload);

            $incomingMessage = $this->addAttachments($incomingMessage);

            $this->messages = [$incomingMessage];
        }

        return $this->messages;
    }

    /**
     * @return bool
     */
    public function isBot() {
        return false;
    }

    /**
     * @param string|CBot_Message_Outgoing_Question|CBot_Message_Outgoing_OutgoingMessage $message
     * @param CBot_Message_Incoming_IncomingMessage                                       $matchingMessage
     * @param array                                                                       $additionalParameters
     *
     * @return CHTTP_Response
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = []) {
        if (!$message instanceof CBot_Contract_WebAccessInterface && !$message instanceof CBot_Message_Outgoing_OutgoingMessage) {
            $this->errorMessage = 'Unsupported message type.';
            $this->replyStatusCode = 500;
        }

        return [
            'message' => $message,
            'additionalParameters' => $additionalParameters,
        ];
    }

    /**
     * @param mixed $payload
     *
     * @return CHTTP_Response
     */
    public function sendPayload($payload) {
        $this->replies[] = $payload;
    }

    /**
     * @param $messages
     *
     * @return array
     */
    protected function buildReply($messages) {
        $replyData = CCollection::make($messages)->transform(function ($replyData) {
            $reply = [];
            $message = $replyData['message'];
            $additionalParameters = $replyData['additionalParameters'];

            if ($message instanceof CBot_Contract_WebAccessInterface) {
                $reply = $message->toWebDriver();
            } elseif ($message instanceof CBot_Message_Outgoing_OutgoingMessage) {
                $attachmentData = (is_null($message->getAttachment())) ? null : $message->getAttachment()->toWebDriver();
                $reply = [
                    'type' => 'text',
                    'text' => $message->getText(),
                    'attachment' => $attachmentData,
                ];
            }
            $reply['additionalParameters'] = $additionalParameters;

            return $reply;
        })->toArray();

        return $replyData;
    }

    /**
     * Send out message response.
     */
    public function messagesHandled() {
        $messages = $this->buildReply($this->replies);

        // Reset replies
        $this->replies = [];

        (new CHTTP_Response(json_encode([
            'status' => $this->replyStatusCode,
            'messages' => $messages,
        ]), $this->replyStatusCode, [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Allow-Origin' => '*',
        ]))->send();
    }

    /**
     * @return bool
     */
    public function isConfigured() {
        return false;
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
        // Not available with the web driver.
    }

    /**
     * Add potential attachments to the message object.
     *
     * @param CBot_Message_Incoming_IncomingMessage $incomingMessage
     *
     * @return CBot_Message_Incoming_IncomingMessage
     */
    protected function addAttachments($incomingMessage) {
        if ($this->files == null) {
            $this->files = c::collect();
        }
        $attachment = $this->event->get('attachment');

        if ($attachment === self::ATTACHMENT_IMAGE) {
            $images = $this->files->map(function ($file) {
                if ($file instanceof CHTTP_UploadedFile) {
                    $path = $file->getRealPath();
                } else {
                    $path = $file['tmp_name'];
                }

                return new CBot_Message_Attachment_Image($this->getDataURI($path));
            })->values()->toArray();
            $incomingMessage->setText(CBot_Message_Attachment_Image::PATTERN);
            $incomingMessage->setImages($images);
        } elseif ($attachment === self::ATTACHMENT_AUDIO) {
            $audio = $this->files->map(function ($file) {
                if ($file instanceof CHTTP_UploadedFile) {
                    $path = $file->getRealPath();
                } else {
                    $path = $file['tmp_name'];
                }

                return new CBot_Message_Attachment_Audio($this->getDataURI($path));
            })->values()->toArray();
            $incomingMessage->setText(CBot_Message_Attachment_Audio::PATTERN);
            $incomingMessage->setAudio($audio);
        } elseif ($attachment === self::ATTACHMENT_VIDEO) {
            $videos = $this->files->map(function ($file) {
                if ($file instanceof CHTTP_UploadedFile) {
                    $path = $file->getRealPath();
                } else {
                    $path = $file['tmp_name'];
                }

                return new CBot_Message_Attachment_Video($this->getDataURI($path));
            })->values()->toArray();
            $incomingMessage->setText(CBot_Message_Attachment_Video::PATTERN);
            $incomingMessage->setVideos($videos);
        } elseif ($attachment === self::ATTACHMENT_FILE) {
            $files = $this->files->map(function ($file) {
                if ($file instanceof CHTTP_UploadedFile) {
                    $path = $file->getRealPath();
                } else {
                    $path = $file['tmp_name'];
                }

                return new CBot_Message_Attachment_File($this->getDataURI($path));
            })->values()->toArray();
            $incomingMessage->setText(CBot_Message_Attachment_File::PATTERN);
            $incomingMessage->setFiles($files);
        }

        return $incomingMessage;
    }

    /**
     * @param $file
     * @param string $mime
     *
     * @return string
     */
    protected function getDataURI($file, $mime = '') {
        return 'data: ' . (function_exists('mime_content_type') ? mime_content_type($file) : $mime) . ';base64,' . base64_encode(file_get_contents($file));
    }
}
