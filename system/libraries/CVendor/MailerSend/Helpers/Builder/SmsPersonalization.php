<?php

use Assert\Assertion;

class CVendor_MailerSend_Helpers_Builder_SmsPersonalization implements CInterface_Arrayable, \JsonSerializable
{
    protected string $recipient;
    protected array $data;

    /**
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function __construct(string $recipient, array $substitutions)
    {
        $this->setRecipient($recipient);
        $this->setData($substitutions);
    }

    /**
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function setRecipient(string $recipient): void
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(static function () use ($recipient) {
            Assertion::startsWith($recipient, '+');
        });

        $this->recipient = $recipient;
    }

    /**
     * @throws MailerSendAssertException
     */
    public function setData(array $data): void
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(static function () use ($data) {
            Assertion::minCount($data, 1);
        });

        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            'phone_number' => $this->recipient,
            'data' => $this->data,
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
