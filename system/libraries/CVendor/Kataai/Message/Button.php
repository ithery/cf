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
            'type' => 'text',
        ] + $this->buttonItem();
    }

    public function buttonItem(): array {
        switch ($this->getType()) {
            case 'payload':
                return [
                    'payload' => $this->getValue()
                ];
            case 'url':
                return [
                    'text' => $this->getValue(),
                ];
            default:
                throw new InvalidArgumentException('Invalid button type: ' . $this->getType());
        }
    }
}
