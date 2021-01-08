<?php
/**
 * @see https://developers.google.com/instance-id/reference/server#results
 */
final class CVendor_Firebase_Messaging_AppInstance implements JsonSerializable {
    /** @var CVendor_Firebase_Messaging_RegistrationToken */
    private $registrationToken;

    /** @var array<string, mixed> */
    private $rawData = [];

    /** @var CVendor_Firebase_Messaging_TopicSubscriptions */
    private $topicSubscriptions;

    private function __construct() {
        $this->topicSubscriptions = new CVendor_Firebase_Messaging_TopicSubscriptions();
    }

    /**
     * @param array<string, mixed> $rawData
     *
     * @internal
     */
    public static function fromRawData(CVendor_Firebase_Messaging_RegistrationToken $registrationToken, array $rawData) {
        $info = new self();

        $info->registrationToken = $registrationToken;
        $info->rawData = $rawData;

        $subscriptions = [];

        foreach ($rawData['rel']['topics'] ?? [] as $topicName => $subscriptionInfo) {
            $topic = CVendor_Firebase_Messaging_Topic::fromValue((string) $topicName);
            $addedAt = CVendor_Firebase_Util_DT::toUTCDateTimeImmutable($subscriptionInfo['addDate'] ?? null);
            $subscriptions[] = new CVendor_Firebase_Messaging_TopicSubscription($topic, $registrationToken, $addedAt);
        }

        $info->topicSubscriptions = new CVendor_Firebase_Messaging_TopicSubscriptions(...$subscriptions);

        return $info;
    }

    public function registrationToken() {
        return $this->registrationToken;
    }

    public function topicSubscriptions() {
        return $this->topicSubscriptions;
    }

    /**
     * @param Topic|string $topic
     */
    public function isSubscribedToTopic($topic) {
        $topic = $topic instanceof CVendor_Firebase_Messaging_Topic ? $topic : CVendor_Firebase_Messaging_Topic::fromValue($topic);

        $filtered = $this->topicSubscriptions->filter(static function (CVendor_Firebase_Messaging_TopicSubscription $subscription) use ($topic) {
            return $topic->value() === $subscription->topic()->value();
        });

        return $filtered->count() > 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function rawData() {
        return $this->rawData;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() {
        return $this->rawData;
    }
}
