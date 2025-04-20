<?php

use Illuminate\Contracts\Support\Arrayable;

class CVendor_MailerSend_Helpers_Builder_Forward implements Arrayable, \JsonSerializable {
    protected string $type;

    protected string $value;

    public function __construct(string $type, string $value) {
        $this->type = $type;
        $this->value = $value;
    }

    public function toArray(): array {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
