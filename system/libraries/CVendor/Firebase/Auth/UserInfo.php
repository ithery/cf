<?php

class CVendor_Firebase_Auth_UserInfo implements \JsonSerializable {
    /**
     * @var null|string
     */
    public $uid = null;

    /**
     * @var null|string
     */
    public $displayName = null;

    /**
     * @var null|string
     */
    public $screenName = null;

    /**
     * @var null|string
     */
    public $email = null;

    /**
     * @var null|string
     */
    public $photoUrl = null;

    /**
     * @var null|string
     */
    public $providerId = null;

    /**
     * @var null|string
     */
    public $phoneNumber = null;

    /**
     * @param array<string, string> $data
     *
     * @return self
     */
    public static function fromResponseData(array $data) {
        $info = new self();
        $info->uid = $data['rawId'] ?? null;
        $info->displayName = $data['displayName'] ?? null;
        $info->screenName = $data['screenName'] ?? null;
        $info->email = $data['email'] ?? null;
        $info->photoUrl = $data['photoUrl'] ?? null;
        $info->providerId = $data['providerId'] ?? null;
        $info->phoneNumber = $data['phoneNumber'] ?? null;

        return $info;
    }

    /**
     * @return array<string, null|string>
     */
    public function jsonSerialize() {
        return \get_object_vars($this);
    }
}
