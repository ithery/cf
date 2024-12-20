<?php

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;

class CEmail_Transport_LogTransport implements TransportInterface {
    /**
     * The Logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Create a new log transport instance.
     *
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return void
     */
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage {
        $string = $message->toString();

        if (str_contains($string, 'Content-Transfer-Encoding: quoted-printable')) {
            $string = quoted_printable_decode($string);
        }

        $this->logger->debug($string);

        return new SentMessage($message, $envelope ?? Envelope::create($message));
    }

    /**
     * Get the logger for the LogTransport instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function logger() {
        return $this->logger;
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string {
        return 'log';
    }
}
