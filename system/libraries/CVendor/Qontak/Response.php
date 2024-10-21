<?php

final class CVendor_Qontak_Response {
    /**
     * @var null|string
     */
    private $messageId;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @var array
     */
    private $data;

    public function __construct(string $messageId = null, string $name = null, array $data = []) {
        $this->messageId = $messageId;
        $this->name = $name;
        $this->data = $data;
    }

    public function getMessageId(): ?string {
        return $this->messageId;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getData(): array {
        return $this->data;
    }
}
