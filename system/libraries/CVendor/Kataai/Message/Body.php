<?php

final class CVendor_Kataai_Message_Body {
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value) {
        $this->value = $value;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function toArray(): array {
        return [
            'value_text' => $this->getValue(),
        ];
    }
}
