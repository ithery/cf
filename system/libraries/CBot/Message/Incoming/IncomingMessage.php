<?php

class CBot_Message_Incoming_IncomingMessage {
    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $sender;

    /**
     * @var string
     */
    protected $recipient;

    /**
     * @var string
     */
    protected $bot_id;

    /**
     * @var array
     */
    protected $images = [];

    /**
     * @var array
     */
    protected $videos = [];

    /**
     * @var mixed
     */
    protected $payload;

    /**
     * @var array
     */
    protected $extras = [];

    /**
     * @var bool
     */
    protected $isFromBot = false;

    /**
     * @var array
     */
    private $audio = [];

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var \BotMan\BotMan\Messages\Attachments\Location
     */
    private $location;

    /**
     * @var \BotMan\BotMan\Messages\Attachments\Contact
     */
    private $contact;

    public function __construct($message, $sender, $recipient, $payload = null, $bot_id = '') {
        $this->message = $message;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->payload = $payload;
        $this->bot_id = $bot_id;
    }

    /**
     * @return string
     */
    public function getRecipient() {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getSender() {
        return $this->sender;
    }

    /**
     * @return mixed
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getConversationIdentifier() {
        return 'conversation-' . $this->bot_id . sha1($this->getSender()) . '-' . sha1($this->getRecipient());
    }

    /**
     * We don't know the user, since conversations are originated on the channel.
     *
     * @return string
     */
    public function getOriginatedConversationIdentifier() {
        return 'conversation-' . $this->bot_id . sha1($this->getSender()) . '-' . sha1('');
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return IncomingMessage
     */
    public function addExtras($key, $value) {
        $this->extras[$key] = $value;

        return $this;
    }

    /**
     * @param null|string $key
     *
     * @return mixed
     */
    public function getExtras($key = null) {
        if (!is_null($key)) {
            return carr::get($this->extras, $key);
        }

        return $this->extras;
    }

    /**
     * @param array $images
     */
    public function setImages(array $images) {
        $this->images = $images;
    }

    /**
     * Returns the message image Objects.
     *
     * @return array
     */
    public function getImages() {
        return $this->images;
    }

    /**
     * @param array $videos
     */
    public function setVideos(array $videos) {
        $this->videos = $videos;
    }

    /**
     * Returns the message video Objects.
     *
     * @return array
     */
    public function getVideos() {
        return $this->videos;
    }

    /**
     * @param array $audio
     */
    public function setAudio(array $audio) {
        $this->audio = $audio;
    }

    /**
     * Returns the message audio Objects.
     *
     * @return array
     */
    public function getAudio() {
        return $this->audio;
    }

    /**
     * @param array $files
     */
    public function setFiles(array $files) {
        $this->files = $files;
    }

    /**
     * @return array
     */
    public function getFiles() {
        return $this->files;
    }

    /**
     * @param \CBot_Message_Attachment_Location $location
     */
    public function setLocation(CBot_Message_Attachment_Location $location) {
        $this->location = $location;
    }

    /**
     * @return \CBot_Message_Attachment_Location
     */
    public function getLocation() {
        if (empty($this->location)) {
            throw new \UnexpectedValueException('This message does not contain a location');
        }

        return $this->location;
    }

    /**
     * @return \CBot_Message_Attachment_Contact
     */
    public function getContact() {
        if (empty($this->contact)) {
            throw new \UnexpectedValueException('This message does not contain a contact');
        }

        return $this->contact;
    }

    /**
     * @param \CBot_Message_Attachment_Contact $contact
     */
    public function setContact(CBot_Message_Attachment_Contact $contact) {
        $this->contact = $contact;
    }

    /**
     * @return bool
     */
    public function isFromBot(): bool {
        return $this->isFromBot;
    }

    /**
     * @param bool $isFromBot
     */
    public function setIsFromBot(bool $isFromBot) {
        $this->isFromBot = $isFromBot;
    }

    /**
     * @param string $message
     */
    public function setText(string $message) {
        $this->message = $message;
    }
}
