<?php

use Laminas\Mail\Message as LaminasMessage;

class CServer_SMTP_Message {
    /**
     * @var string
     */
    protected $raw;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var array
     */
    protected $to;

    /**
     * Message constructor.
     *
     * @param string $raw
     * @param string $from
     * @param array  $to
     */
    public function __construct($raw, $from, $to) {
        $this->raw = $raw;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Store the raw message in a given filesystem.
     *
     * @param CStorage_FilesystemInterface $filesystem
     * @param null                         $path
     * @param array                        $options
     *
     * @return null|string
     */
    public function store(CStorage_FilesystemInterface $filesystem, $path = null, $options = []) {
        if (is_null($path)) {
            $path = uniqid('msg_') . '.eml';
        }
        $filesystem->put($path, $this->getRaw(), $options);

        return $path;
    }

    /**
     * Get the raw message content.
     *
     * @return string
     */
    public function getRaw(): string {
        return $this->raw;
    }

    /**
     * Create a mailable from the message.
     *
     * @return CServer_SMTP_Mailable
     */
    public function makeMailable()  {
        return (new CServer_SMTP_Mailable())
            ->setLaminasMessage($this->getLaminasMessage());
    }

    /**
     * Get the zend message.
     *
     * @return LaminasMessage
     */
    public function getLaminasMessage() {
        return LaminasMessage::fromString($this->raw)
            ->setFrom($this->from)
            ->setTo($this->to);
    }
}
