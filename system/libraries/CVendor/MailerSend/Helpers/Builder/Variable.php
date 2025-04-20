<?php

use Assert\Assertion;
use Illuminate\Contracts\Support\Arrayable;

class CVendor_MailerSend_Helpers_Builder_Variable implements Arrayable, \JsonSerializable {
    protected string $email;

    protected array $substitutions;

    /**
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     */
    public function __construct(string $email, array $substitutions) {
        $this->setEmail($email);
        $this->setSubstitutions($substitutions);
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
    public function setSubstitutions(array $substitutions): void {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(static function () use ($substitutions) {
            Assertion::minCount($substitutions, 1);
        });

        $mapped = [];

        foreach ($substitutions as $var => $value) {
            $mapped[] = [
                'var' => $var,
                'value' => $value,
            ];
        }

        $this->substitutions = $mapped;
    }

    public function toArray(): array {
        return [
            'email' => $this->email,
            'substitutions' => $this->substitutions,
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
