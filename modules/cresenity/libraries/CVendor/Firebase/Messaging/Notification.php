<?php

class CVendor_Firebase_Messaging_Notification implements \JsonSerializable {
    /** @var string|null */
    private $title;

    /** @var string|null */
    private $body;

    /** @var string|null */
    private $imageUrl;

    private function __construct($title = null, $body = null, $imageUrl = null) {
        $this->title = $title;
        $this->body = $body;
        $this->imageUrl = $imageUrl;

        if ($this->title === null && $this->body === null) {
            throw new InvalidArgumentException('The title and body of a notification cannot both be NULL');
        }
    }

    public static function create($title = null, $body = null, $imageUrl = null) {
        return new self($title, $body, $imageUrl);
    }

    public static function fromArray(array $data) {
        try {
            return new self(
                carr::get($data, 'title'),
                carr::get($data, 'body'),
                carr::get($data, 'image')
            );
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function withTitle($title) {
        $notification = clone $this;
        $notification->title = $title;

        return $notification;
    }

    public function withBody($body) {
        $notification = clone $this;
        $notification->body = $body;

        return $notification;
    }

    public function withImageUrl($imageUrl) {
        $notification = clone $this;
        $notification->imageUrl = $imageUrl;

        return $notification;
    }

    /**
     * @return string|null
     */
    public function title() {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function body() {
        return $this->body;
    }

    /**
     * @return string|null
     */
    public function imageUrl() {
        return $this->imageUrl;
    }

    public function jsonSerialize() {
        return \array_filter([
            'title' => $this->title,
            'body' => $this->body,
            'image' => $this->imageUrl,
        ], static function ($value) {
            return $value !== null;
        });
    }
}
