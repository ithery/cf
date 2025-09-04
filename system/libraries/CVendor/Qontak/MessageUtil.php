<?php

final class CVendor_Qontak_MessageUtil {
    /**
     * @var Message
     */
    private $message;

    public function __construct(CVendor_Qontak_Message $message) {
        $this->message = $message;
    }

    public static function makeRequestBody(CVendor_Qontak_Message $message): array {
        return (new self($message))->toRequestBody();
    }

    public function toRequestBody(): array {
        $params = [
            'body' => $this->makeBodyParams(),
            'buttons' => $this->makeButtonParams(),
        ];

        if ($this->message->getHeader()) {
            $params['header'] = $this->message->getHeader()->toArray();
        }

        return [
            'to_name' => $this->message->getReceiver()->getName(),
            'to_number' => $this->message->getReceiver()->getTo(),
            'language' => [
                'code' => $this->message->getLanguage()->getCode(),
            ],
            'parameters' => $params,
        ];
    }

    private function makeBodyParams(): array {
        $params = [];

        foreach ($this->message->getBody() as $key => $body) {
            $iteration = (int) $key + 1;

            $params[] = [
                'key' => $iteration,
                'value' => \sprintf('param%s', $iteration),
            ] + $body->toArray();
        }

        return $params;
    }

    private function makeButtonParams(): array {
        $buttons = [];

        foreach ($this->message->getButtons() as $key => $button) {
            $buttons[] = [
                'index' => (string) $key,
            ] + $button->toArray();
        }

        return $buttons;
    }
}
