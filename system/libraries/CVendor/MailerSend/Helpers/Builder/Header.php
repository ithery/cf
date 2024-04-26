<?php

use Assert\Assertion;

class CVendor_MailerSend_Helpers_Builder_Header implements CInterface_Arrayable, JsonSerializable {
    protected string $name;

    protected string $value;

    /**
     * @throws MailerSendAssertException
     */
    public function __construct(string $name, string $value) {
        $this->setName($name);
        $this->setValue($value);
    }

    /**
     * @throws MailerSendAssertException
     */
    public function setName(string $name): void {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(static function () use ($name) {
            Assertion::notEmpty($name);
            Assertion::string($name);
        });

        $this->name = $name;
    }

    /**
     * @throws MailerSendAssertException
     */
    public function setValue(string $value): void {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(static function () use ($value) {
            Assertion::notEmpty($value);
            Assertion::string($value);
        });

        $this->value = $value;
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
