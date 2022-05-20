<?php

class CVendor_Firebase_Auth_UserMetaData implements \JsonSerializable {
    /**
     * @var null|DateTimeImmutable
     */
    public $createdAt = null;

    /**
     * @var null|DateTimeImmutable
     */
    public $lastLoginAt = null;

    /**
     * @var null|DateTimeImmutable
     */
    public $passwordUpdatedAt = null;

    /**
     * The time at which the user was last active (ID token refreshed), or null
     * if the user was never active.
     *
     * @var null|DateTimeImmutable
     */
    public $lastRefreshAt = null;

    /**
     * @param array<string, mixed> $data
     *
     * @return self
     */
    public static function fromResponseData(array $data) {
        $metadata = new self();
        $metadata->createdAt = CVendor_Firebase_Util_DT::toUTCDateTimeImmutable($data['createdAt']);

        if ($data['lastLoginAt'] ?? null) {
            $metadata->lastLoginAt = CVendor_Firebase_Util_DT::toUTCDateTimeImmutable($data['lastLoginAt']);
        }

        if ($data['passwordUpdatedAt'] ?? null) {
            $metadata->passwordUpdatedAt = CVendor_Firebase_Util_DT::toUTCDateTimeImmutable($data['passwordUpdatedAt']);
        }

        if ($data['lastRefreshAt'] ?? null) {
            $metadata->lastRefreshAt = CVendor_Firebase_Util_DT::toUTCDateTimeImmutable($data['lastRefreshAt']);
        }

        return $metadata;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() {
        $data = \get_object_vars($this);

        $data['createdAt'] = $this->createdAt !== null ? $this->createdAt->format(DATE_ATOM) : null;
        $data['lastLoginAt'] = $this->lastLoginAt !== null ? $this->lastLoginAt->format(DATE_ATOM) : null;

        return $data;
    }
}
