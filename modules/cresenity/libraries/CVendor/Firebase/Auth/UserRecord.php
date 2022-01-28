<?php

class CVendor_Firebase_Auth_UserRecord implements \JsonSerializable {
    /**
     * @var string
     */
    public $uid;

    /**
     * @var bool
     */
    public $emailVerified = false;

    /**
     * @var bool
     */
    public $disabled = false;

    /**
     * @var CVendor_Firebase_Auth_UserMetaData
     */
    public $metadata;

    /**
     * @var null|string
     */
    public $email = null;

    /**
     * @var null|string
     */
    public $displayName = null;

    /**
     * @var null|string
     */
    public $photoUrl = null;

    /**
     * @var null|string
     */
    public $phoneNumber = null;

    /**
     * @var CVendor_Firebase_Auth_UserInfo[]
     */
    public $providerData = [];

    /**
     * @var null|string
     */
    public $passwordHash = null;

    /**
     * @var null|string
     */
    public $passwordSalt = null;

    /**
     * @var array<string, mixed>
     */
    public $customClaims = [];

    /**
     * @var null|DateTimeImmutable
     */
    public $tokensValidAfterTime = null;

    /**
     * @var null|string
     */
    public $tenantId = null;

    public function __construct() {
        $this->metadata = new CVendor_Firebase_Auth_UserMetaData();
        $this->uid = '';
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromResponseData(array $data): self {
        $record = new self();
        $record->uid = $data['localId'] ?? '';
        $record->email = $data['email'] ?? null;
        $record->emailVerified = $data['emailVerified'] ?? $record->emailVerified;
        $record->displayName = $data['displayName'] ?? null;
        $record->photoUrl = $data['photoUrl'] ?? null;
        $record->phoneNumber = $data['phoneNumber'] ?? null;
        $record->disabled = $data['disabled'] ?? $record->disabled;
        $record->metadata = self::userMetaDataFromResponseData($data);
        $record->providerData = self::userInfoFromResponseData($data);
        $record->passwordHash = $data['passwordHash'] ?? null;
        $record->passwordSalt = $data['salt'] ?? null;
        $record->tenantId = $data['tenantId'] ?? $data['tenant_id'] ?? null;

        if ($data['validSince'] ?? null) {
            $record->tokensValidAfterTime = CVendor_Firebase_Util_DT::toUTCDateTimeImmutable($data['validSince']);
        }

        if ($customClaims = $data['customClaims'] ?? $data['customAttributes'] ?? '{}') {
            $record->customClaims = CVendor_Firebase_Util_JSON::decode($customClaims, true);
        }

        return $record;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return CVendor_Firebase_Auth_UserMetaData
     */
    private static function userMetaDataFromResponseData(array $data) {
        return CVendor_Firebase_Auth_UserMetaData::fromResponseData($data);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<int, CVendor_Firebase_Auth_UserInfo>
     */
    private static function userInfoFromResponseData(array $data) {
        return \array_map(
            static fn (array $userInfoData) => CVendor_Firebase_Auth_UserInfo::fromResponseData($userInfoData),
            $data['providerUserInfo'] ?? []
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array {
        $data = \get_object_vars($this);

        $data['tokensValidAfterTime'] = $this->tokensValidAfterTime !== null
            ? $this->tokensValidAfterTime->format(DATE_ATOM)
            : null;

        return $data;
    }

    /**
     * @return mixed
     */
    public function __get(string $name) {
        return $this->{$name};
    }
}
