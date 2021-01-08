<?php

final class CVendor_Firebase_Messaging_TopicSubscription implements JsonSerializable {
    /** @var CVendor_Firebase_Messaging_Topic */
    private $topic;

    /** @var CVendor_Firebase_Messaging_RegistrationToken */
    private $registrationToken;

    /** @var DateTimeImmutable */
    private $subscribedAt;

    public function __construct(CVendor_Firebase_Messaging_Topic $topic, CVendor_Firebase_Messaging_RegistrationToken $registrationToken, DateTimeImmutable $subscribedAt) {
        $this->topic = $topic;
        $this->registrationToken = $registrationToken;
        $this->subscribedAt = $subscribedAt;
    }

    public function topic() {
        return $this->topic;
    }

    public function registrationToken() {
        return $this->registrationToken;
    }

    public function subscribedAt(): DateTimeImmutable {
        return $this->subscribedAt;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize() {
        return [
            'topic' => $this->topic->value(),
            'registration_token' => $this->registrationToken->value(),
            'subscribed_at' => $this->subscribedAt->format(\DATE_ATOM),
        ];
    }
}
