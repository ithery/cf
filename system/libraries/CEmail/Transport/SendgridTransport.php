<?php

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mailer\Header\MetadataHeader;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Exception\TransportException;

class CEmail_Transport_SendgridTransport extends AbstractTransport {
    const REQUEST_BODY_PARAMETER = 'sendgrid/request-body-parameter';

    /**
     * The Amazon SES instance.
     *
     * @var \CVendor_Sendgrid
     */
    protected $sendgrid;

    /**
     * The Amazon SES transmission options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new Sendgrid transport instance.
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct(CVendor_SendGrid $sendgrid, $options = []) {
        $this->options = $options;
        $this->sendgrid = $sendgrid;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function doSend(SentMessage $message): void {
        $originalMessage = $message->getOriginalMessage();
        /** @var \Symfony\Component\Mime\Email $originalMessage */
        $sendgridEmail = new CVendor_SendGrid_Mail_Mail();
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $sendgridEmail->addPersonalization($this->getPersonalization($email));
        $sendgridEmail->setFrom($this->getFrom($email));
        if ($replyTo = $this->getReplyTo($email)) {
            $sendgridEmail->setReplyTo($replyTo);
        }
        $sendgridEmail->setSubject($email->getSubject());

        foreach ($this->getContents($email) as $content) {
            $sendgridEmail->addContent($content);
        }
        foreach ($this->getAttachments($email) as $attachment) {
            $sendgridEmail->addAttachment($attachment);
        }

        $response = $this->sendgrid->send($sendgridEmail);
        if ($response->statusCode() > 400) {
            throw new Exception('Fail to send mail, API Response:(' . $response->statusCode() . ')' . $response->body());
        }
        $headers = $response->headers(true);
        $originalMessage
            ->getHeaders()
            ->addTextHeader('X-Sendgrid-Message-Id', carr::get($headers, 'X-Message-Id'));
        $originalMessage
            ->getHeaders()
            ->addTextHeader('X-Message-Id', carr::get($headers, 'X-Message-Id'));
    }

    /**
     * Get the Amazon SES client for the SesTransport instance.
     *
     * @return \CVendor_Sendgrid
     */
    public function sendgrid() {
        return $this->sendgrid;
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
        return 'sendgrid';
    }

    /**
     * @param Email $email
     *
     * @return null|CVendor_SendGrid_Mail_From
     */
    private function getFrom(Email $email) {
        if (count($email->getFrom()) > 0) {
            foreach ($email->getFrom() as $from) {
                return new CVendor_SendGrid_Mail_From($from->getAddress(), $from->getName());
            }
        }

        return null;
    }

    /**
     * @param Email $email
     *
     * @return null|CVendor_SendGrid_Mail_ReplyTo
     */
    protected function getReplyTo(Email $email) {
        if (count($email->getReplyTo()) > 0) {
            $replyTo = $email->getReplyTo()[0];

            return new CVendor_SendGrid_Mail_ReplyTo($replyTo->getAddress(), $replyTo->getName());
        }

        return null;
    }

    /**
     * @param Address[] $addresses
     *
     * @return CVendor_SendGrid_Mail_EmailAddress[]
     */
    private function getAddress(array $addresses): array {
        $recipients = [];
        foreach ($addresses as $address) {
            $recipient = new CVendor_SendGrid_Mail_EmailAddress($address->getAddress());
            if ($address->getName() !== '') {
                $recipient->setName($address->getName());
            }
            $recipients[] = $recipient;
        }

        return $recipients;
    }

    /**
     * @param Email $email
     *
     * @return CVendor_SendGrid_Mail_Personalization
     */
    private function getPersonalization(Email $email) {
        $personalization = new CVendor_SendGrid_Mail_Personalization();
        foreach ($this->getAddress($email->getTo()) as $address) {
            $personalization->addTo($address);
        }

        if (count($email->getCc()) > 0) {
            foreach ($this->getAddress($email->getCc()) as $address) {
                $personalization->addCc($address);
            }
        }

        if (count($email->getBcc()) > 0) {
            foreach ($this->getAddress($email->getBcc()) as $address) {
                $personalization->addBcc($address);
            }
        }

        return $personalization;
    }

    /**
     * @param Email $email
     *
     * @return CVendor_SendGrid_Mail_Content[]
     */
    private function getContents(Email $email): array {
        $contents = [];
        if (!is_null($email->getTextBody())) {
            $contents[] = new CVendor_SendGrid_Mail_Content('text/plain', $email->getTextBody());
        }

        if (!is_null($email->getHtmlBody())) {
            $contents[] = new CVendor_SendGrid_Mail_Content('text/html', $email->getHtmlBody());
        }

        return $contents;
    }

    /**
     * @param Email $email
     *
     * @return array
     */
    private function getAttachments(Email $email): array {
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $filename = $this->getAttachmentName($attachment);
            if ($filename === self::REQUEST_BODY_PARAMETER) {
                continue;
            }
            $sendgridAttachment = new CVendor_SendGrid_Mail_Attachment();
            $sendgridAttachment->setContent($attachment->getBody());
            $sendgridAttachment->setFilename($this->getAttachmentName($attachment));
            $sendgridAttachment->setType($this->getAttachmentContentType($attachment));
            $sendgridAttachment->setDisposition($attachment->getPreparedHeaders()->getHeaderParameter('Parameterized', 'Content-Disposition'));
            $sendgridAttachment->setContentID($attachment->getContentId());
            $attachments[] = $sendgridAttachment;
        }

        return $attachments;
    }

    private function getAttachmentName(DataPart $dataPart): string {
        return $dataPart->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename');
    }

    private function getAttachmentContentType(Datapart $dataPart): string {
        return $dataPart->getMediaType() . '/' . $dataPart->getMediaSubtype();
    }
}
