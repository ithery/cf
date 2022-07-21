<?php

use Swift_Message as SwiftMessage;
use Laminas\Mail\Message as LaminasMessage;
use Goetas\Mail\ToSwiftMailParser\MimeParser;
use Swift_Mime_SimpleMimeEntity as MessagePart;

class CServer_SMTP_Mailable extends CEmail_Mailable {
    /**
     * @var null|LaminasMessage
     */
    protected $laminasMessage;

    /**
     * Get the raw message content.
     *
     * @return string
     */
    public function getRawMessage() {
        return $this->getLaminasMessage() ? $this->getLaminasMessage()->toString() : '';
    }

    /**
     * Get the laminas message.
     *
     * @return null|LaminasMessage
     */
    public function getLaminasMessage() {
        return $this->laminasMessage;
    }

    /**
     * Set the laminas message.
     *
     * @param LaminasMessage $laminasMessage
     *
     * @return $this
     */
    public function setLaminasMessage(LaminasMessage $laminasMessage) {
        $this->laminasMessage = $laminasMessage;

        $this->parseLaminasMessage();

        return $this;
    }

    /**
     * Parse the laminas message.
     *
     * @return void
     */
    protected function parseLaminasMessage() {
        $laminasMessage = $this->getLaminasMessage();

        if (is_null($laminasMessage)) {
            return;
        }

        $this->parseAddresses($laminasMessage);
        $this->parseSubject($laminasMessage);
        $this->parseHeaders($laminasMessage);
        $this->parseContent($laminasMessage);
    }

    /**
     * Parse the addresses from the laminas message.
     *
     * @param LaminasMessage $message
     */
    private function parseAddresses(LaminasMessage $message) {
        foreach ($message->getFrom() as $from) {
            $this->from($from->getEmail(), $from->getName());
        }
        foreach ($message->getTo() as $to) {
            $this->to($to->getEmail(), $to->getName());
        }
        foreach ($message->getCc() as $cc) {
            $this->cc($cc->getEmail(), $cc->getName());
        }
        foreach ($message->getBcc() as $bcc) {
            $this->bcc($bcc->getEmail(), $bcc->getName());
        }
        foreach ($message->getReplyTo() as $replyTo) {
            $this->replyTo($replyTo->getEmail(), $replyTo->getName());
        }
    }

    /**
     * Parse the message subject.
     *
     * @param LaminasMessage $message
     */
    private function parseSubject(LaminasMessage $message) {
        $this->subject($message->getSubject());
    }

    /**
     * Parse the headers.
     *
     * @param LaminasMessage $message
     */
    private function parseHeaders(LaminasMessage $message) {
        $headers = $message->getHeaders()->toArray();
        $this->withSwiftMessage(function (SwiftMessage $message) use ($headers) {
            foreach ($headers as $name => $value) {
                if (is_array($value)) {
                    $value = implode("\n", $value);
                }
                $message->getHeaders()->addTextHeader($name, $value);
            }
        });
    }

    /**
     * Parse the content of the message.
     *
     * @param LaminasMessage $message
     */
    private function parseContent(LaminasMessage $message) {
        foreach ($this->parseMimeMessage($message->toString()) as $part) {
            if ($part->getContentType() == 'text/html') {
                $this->html($part->getBody());
            } elseif ($part->getContentType() == 'text/plain') {
                $this->text($part->getBody());
            } else {
                $this->attachMimeEntity($part);
            }
        }

        // Set the fallback view
        if (!$this->html) {
            $this->html($message->getBodyText());
        }
    }

    /**
     * @param string $content
     *
     * @return MessagePart[]
     */
    private function parseMimeMessage(string $content): array {
        $mimeMessage = (new MimeParser())
            ->parseString($content);

        $parts = $this->getMimePartChildren($mimeMessage);

        foreach ($parts as $index => $part) {
            if (strpos($part->getContentType(), 'multipart') === 0) {
                // Remove multipart types
                unset($parts[$index]);
            } elseif (empty($part->getContentType())) {
                // Remove empty content
                unset($parts[$index]);
            }
        }

        return $parts;
    }

    /**
     * Get all children from a message part.
     *
     * @param MessagePart $part
     *
     * @return MessagePart[]
     */
    private function getMimePartChildren(MessagePart $part): array {
        $children = [];
        foreach ($part->getChildren() as $child) {
            $children[] = $child;
            $children = array_merge($children, $this->getMimePartChildren($child));
        }

        return $children;
    }

    /**
     * Attach a swift mime entity.
     *
     * @param MessagePart $part
     *
     * @return $this
     */
    protected function attachMimeEntity(MessagePart $part) {
        return $this->withSwiftMessage(function (SwiftMessage $message) use ($part) {
            $message->attach($part);
        });
    }

    /**
     * Build the final message.
     *
     * @return void
     */
    public function build() {
        return;
    }
}
