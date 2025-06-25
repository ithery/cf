<?php

final class CVendor_Kataai_MessageUtil {
    /**
     * @var Message
     */
    private $message;

    public function __construct(CVendor_Kataai_Message $message) {
        $this->message = $message;
    }

    public static function makeRequestComponent(CVendor_Kataai_Message $message): array {
        return (new self($message))->toRequestComponents();
    }

    public function toRequestComponents(): array {
        $params = [];

        if ($this->message->getHeader()) {
            $params[] = [
                'type' => 'header',
                'parameters' => $this->message->getHeader()->toArray()
            ];
        }

        if ($this->message->getBody()) {
            $params[] = [
                'type' => 'body',
                'parameters' => $this->makeBodyParams()
            ];
        }

        if ($this->message->getButtons()) {
            $params= array_merge($params, $this->makeButtonParams());
        }

        return $params;
    }

    private function makeBodyParams(): array {
        $params = [];

        foreach ($this->message->getBody() as $key => $body) {
            $params[] = [
                'type' => 'text',
                'text' => \sprintf($body->getValue()),
            ];
        }

        return $params;
    }

    private function makeButtonParams(): array {
        $buttons = [];

        foreach ($this->message->getButtons() as $key => $button) {
            $subtype = $button->getType() === 'payload' ? 'quick_reply' : 'url';
            $arrButton = [
                'type' => 'button',
                'sub_type' => $subtype,
                'index' => (string) $key,
            ];
            $arrButton['parameters'][] = $button->toArray();
            $buttons[] = $arrButton;
        }

        return $buttons;
    }
}
