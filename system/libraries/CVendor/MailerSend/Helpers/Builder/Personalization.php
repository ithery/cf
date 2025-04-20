<?php

use Assert\Assertion;
use Illuminate\Contracts\Support\Arrayable;

class CVendor_MailerSend_Helpers_Builder_Personalization implements Arrayable, \JsonSerializable {
    protected string $email;

    protected array $data;

    /**
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function __construct(string $email, array $substitutions) {
        $this->setEmail($email);
        $this->setData($substitutions);
    }

    /**
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function setEmail(string $email): void {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(static function () use ($email) {
            Assertion::email($email);
        });

        $this->email = $email;
    }

    /**
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function setData(array $data): void {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(static function () use ($data) {
            Assertion::minCount($data, 1);
        });

        $this->data = $data;
    }

    public function toArray(): array {
        return [
            'email' => $this->email,
            'data' => $this->data,
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
