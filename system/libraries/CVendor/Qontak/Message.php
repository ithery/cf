<?php

use Webmozart\Assert\Assert;

final class CVendor_Qontak_Message {
    /**
     * @var CVendor_Qontak_Message_Receiver
     */
    private $receiver;

    /**
     * @var CVendor_Qontak_Message_Language
     */
    private $language;

    /**
     * @var null|CVendor_Qontak_Message_Header
     */
    private $header;

    /**
     * @var CVendor_Qontak_Message_Body[]
     */
    private $body;

    /**
     * @var CVendor_Qontak_Message_Button[]
     */
    private $buttons;

    public function __construct(?CVendor_Qontak_Message_Receiver $receiver = null, ?CVendor_Qontak_Message_Language $language = null, array $body = [], ?CVendor_Qontak_Message_Header $header = null, array $buttons = []) {
        $this->receiver = $receiver;

        $this->language = $language ?? new CVendor_Qontak_Message_Language('id');

        $this->header = $header;

        Assert::allIsInstanceOf($body, CVendor_Qontak_Message_Body::class);
        $this->body = $body;

        Assert::allIsInstanceOf($buttons, CVendor_Qontak_Message_Button::class);
        $this->buttons = $buttons;
    }

    public function getReceiver(): CVendor_Qontak_Message_Receiver {
        return $this->receiver;
    }

    public function getLanguage(): CVendor_Qontak_Message_Language {
        return $this->language;
    }

    public function getHeader(): ?CVendor_Qontak_Message_Header {
        return $this->header;
    }

    /**
     * @return CVendor_Qontak_Message_Body[]
     */
    public function getBody(): array {
        return $this->body;
    }

    /**
     * @return CVendor_Qontak_Message_Button[]
     */
    public function getButtons(): array {
        return $this->buttons;
    }

    public function setReceiver(string $to, string $name) {
        $this->receiver = new CVendor_Qontak_Message_Receiver($to, $name);

        return $this;
    }

    public function setLanguage(string $code) {
        $this->language = new CVendor_Qontak_Message_Language($code);

        return $this;
    }

    public function addBody(string $value) {
        $this->body[] = new CVendor_Qontak_Message_Body($value);

        return $this;
    }

    public function setHeader(string $format, string $url, string $filename) {
        $this->header = new CVendor_Qontak_Message_Header($format, $url, $filename);

        return $this;
    }

    public function addButton(string $type, string $value) {
        $this->buttons[] = new CVendor_Qontak_Message_Button($type, $value);

        return $this;
    }
}
