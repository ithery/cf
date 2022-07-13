<?php

class CBot_Message_Outgoing_OutgoingMessage {
    /**
     * @var string
     */
    protected $message;

    /**
     * @var \CBot_Message_AttachmentAbstract
     */
    protected $attachment;

    /**
     * IncomingMessage constructor.
     *
     * @param string                          $message
     * @param CBot_Message_AttachmentAbstract $attachment
     */
    public function __construct($message = null, CBot_Message_AttachmentAbstract $attachment = null) {
        $this->message = $message;
        $this->attachment = $attachment;
    }

    /**
     * @param string                          $message
     * @param CBot_Message_AttachmentAbstract $attachment
     *
     * @return CBot_Message_Outgoing_OutgoingMessage
     */
    public static function create($message = null, CBot_Message_AttachmentAbstract $attachment = null) {
        return new static($message, $attachment);
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function text($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * @param \CBot_Message_AttachmentAbstract $attachment
     *
     * @return $this
     */
    public function withAttachment(CBot_Message_AttachmentAbstract $attachment) {
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * @return \CBot_Message_AttachmentAbstract
     */
    public function getAttachment() {
        return $this->attachment;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->message;
    }
}
