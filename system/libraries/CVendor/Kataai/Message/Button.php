<?php

final class CVendor_Kataai_Message_Button {
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    public function __construct(string $type, string $value) {
        $this->type = $type;
        $this->value = $value;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function toArray(): array {
        return [
            'type' => $this->getType(),
            'value' => $this->getValue(),
        ];
    }
}
