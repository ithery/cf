<?php

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;

class CEmail_Transport_ArrayTransport implements TransportInterface {
    /**
     * The collection of Symfony Messages.
     *
     * @var \CCollection
     */
    protected $messages;

    /**
     * Create a new array transport instance.
     *
     * @return void
     */
    public function __construct() {
        $this->messages = new CCollection();
    }

    /**
     * @inheritdoc
     */
    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage {
        return $this->messages[] = new SentMessage($message, $envelope ?? Envelope::create($message));
    }

    /**
     * Retrieve the collection of messages.
     *
     * @return \CCollection
     */
    public function messages() {
        return $this->messages;
    }

    /**
     * Clear all of the messages from the local collection.
     *
     * @return \CCollection
     */
    public function flush() {
        return $this->messages = new CCollection();
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string {
        return 'array';
    }
}
