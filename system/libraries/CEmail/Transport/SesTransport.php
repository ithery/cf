<?php

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Header\MetadataHeader;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Exception\TransportException;

class CEmail_Transport_SesTransport extends AbstractTransport {
    /**
     * The Amazon SES instance.
     *
     * @var \Aws\Ses\SesClient
     */
    protected $ses;

    /**
     * The Amazon SES transmission options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new SES transport instance.
     *
     * @param \Aws\Ses\SesClient $ses
     * @param array              $options
     *
     * @return void
     */
    public function __construct(SesClient $ses, $options = []) {
        $this->ses = $ses;
        $this->options = $options;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function doSend(SentMessage $message): void {
        $options = $this->options;
        $originalMessage = $message->getOriginalMessage();
        /** @var \Symfony\Component\Mime\Email $originalMessage */
        if ($originalMessage instanceof Message) {
            /** @var \Symfony\Component\Mime\Email $originalMessage */
            foreach ($originalMessage->getHeaders()->all() as $header) {
                if ($header instanceof MetadataHeader) {
                    $options['Tags'][] = ['Name' => $header->getKey(), 'Value' => $header->getValue()];
                }
            }
        }

        try {
            $result = $this->ses->sendRawEmail(
                array_merge(
                    $options,
                    [
                        'Source' => $message->getEnvelope()->getSender()->toString(),
                        'Destinations' => c::collect($message->getEnvelope()->getRecipients())
                            ->map
                            ->toString()
                            ->values()
                            ->all(),
                        'RawMessage' => [
                            'Data' => $message->toString(),
                        ],
                    ]
                )
            );
        } catch (AwsException $e) {
            $reason = $e->getAwsErrorMessage() ?? $e->getMessage();

            throw new TransportException(
                sprintf('Request to AWS SES API failed. Reason: %s.', $reason),
                is_int($e->getCode()) ? $e->getCode() : 0,
                $e
            );
        }

        $messageId = $result->get('MessageId');

        $originalMessage->getHeaders()->addHeader('X-Message-ID', $messageId);
        $originalMessage->getHeaders()->addHeader('X-SES-Message-ID', $messageId);
    }

    /**
     * Get the Amazon SES client for the SesTransport instance.
     *
     * @return \Aws\Ses\SesClient
     */
    public function ses() {
        return $this->ses;
    }

    /**
     * Get the transmission options being used by the transport.
     *
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Set the transmission options being used by the transport.
     *
     * @param array $options
     *
     * @return array
     */
    public function setOptions(array $options) {
        return $this->options = $options;
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string {
        return 'ses';
    }
}
