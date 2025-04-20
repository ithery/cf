<?php

use Assert\Assertion;
use Illuminate\Contracts\Support\Arrayable;

class CVendor_MailerSend_Helpers_Builder_Recipient implements Arrayable, \JsonSerializable {
    protected ?string $name;

    protected string $email;

    /**
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function __construct(string $email, ?string $name) {
        $this->setEmail($email);
        $this->setName($name);
    }

    public function setName(?string $name): void {
        $this->name = $name;
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

    public function toArray(): array {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
