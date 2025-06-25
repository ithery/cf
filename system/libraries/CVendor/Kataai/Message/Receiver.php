<?php

final class CVendor_Kataai_Message_Receiver {
    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $name;

    public function __construct(string $to, string $name) {
        $this->to = $to;
        $this->name = $name;
    }

    public function getTo(): string {
        return $this->to;
    }

    public function getName(): string {
        return $this->name;
    }
}
