<?php

/**
 * @codeCoverageIgnore
 * @template T
 */
trait CVendor_Firebase_Request_Trait_EditUserTrait {
    /**
     * @var null|string
     */
    protected $uid = null;

    /**
     * @var null|string
     */
    protected $email = null;

    /**
     * @var null|string
     */
    protected $displayName = null;

    /**
     * @var null|bool
     */
    protected $emailIsVerified = null;

    /**
     * @var null|string
     */
    protected $phoneNumber = null;

    /**
     * @var null|string
     */
    protected $photoUrl = null;

    /**
     * @var null|bool
     */
    protected $markAsEnabled = null;

    /**
     * @var null|bool
     */
    protected $markAsDisabled = null;

    /**
     * @var null|string
     */
    protected $clearTextPassword = null;

    /**
     * @param self                 $request
     * @param array<string, mixed> $properties
     *
     * @throws CVendor_Firebase_Exception_InvalidArgumentException when invalid properties have been provided
     *
     * @return self
     */
    protected static function withEditableProperties(self $request, array $properties) {
        foreach ($properties as $key => $value) {
            switch (\mb_strtolower((string) \preg_replace('/[^a-z]/i', '', $key))) {
                case 'uid':
                case 'localid':
                    $request = $request->withUid($value);

                    break;
                case 'email':
                    $request = $request->withEmail($value);

                    break;
                case 'unverifiedemail':
                    $request = $request->withUnverifiedEmail($value);

                    break;
                case 'verifiedemail':
                    $request = $request->withVerifiedEmail($value);

                    break;
                case 'emailverified':
                    if ($value === true) {
                        $request = $request->markEmailAsVerified();
                    } elseif ($value === false) {
                        $request = $request->markEmailAsUnverified();
                    }

                    break;
                case 'displayname':
                    $request = $request->withDisplayName($value);

                    break;
                case 'phone':
                case 'phonenumber':
                    $request = $request->withPhoneNumber($value);

                    break;
                case 'photo':
                case 'photourl':
                    $request = $request->withPhotoUrl($value);

                    break;
                case 'disableuser':
                case 'disabled':
                case 'isdisabled':
                    if ($value === true) {
                        $request = $request->markAsDisabled();
                    } elseif ($value === false) {
                        $request = $request->markAsEnabled();
                    }

                    break;
                case 'enableuser':
                case 'enabled':
                case 'isenabled':
                    if ($value === true) {
                        $request = $request->markAsEnabled();
                    } elseif ($value === false) {
                        $request = $request->markAsDisabled();
                    }

                    break;
                case 'password':
                case 'cleartextpassword':
                    $request = $request->withClearTextPassword($value);

                    break;
            }
        }

        return $request;
    }

    /**
     * @param \Stringable|mixed $uid
     *
     * @return self
     */
    public function withUid($uid) {
        $request = clone $this;
        $request->uid = (string) (new CVendor_Firebase_Value_Uid((string) $uid));

        return $request;
    }

    /**
     * @param \Stringable|string $email
     *
     * @return self
     */
    public function withEmail($email) {
        $request = clone $this;
        $request->email = (string) (new CVendor_Firebase_Value_Email((string) $email));

        return $request;
    }

    /**
     * @param \Stringable|string $email
     *
     * @return self
     */
    public function withVerifiedEmail($email) {
        $request = clone $this;
        $request->email = (string) (new CVendor_Firebase_Value_Email((string) $email));
        $request->emailIsVerified = true;

        return $request;
    }

    /**
     * @param \Stringable|string $email
     *
     * @return self
     */
    public function withUnverifiedEmail($email) {
        $request = clone $this;
        $request->email = (string) (new Email((string) $email));
        $request->emailIsVerified = false;

        return $request;
    }

    /**
     * @return self
     */
    public function withDisplayName(string $displayName) {
        $request = clone $this;
        $request->displayName = $displayName;

        return $request;
    }

    /**
     * @param null|\Stringable|string $phoneNumber
     *
     * @return self
     */
    public function withPhoneNumber($phoneNumber) {
        $phoneNumber = $phoneNumber !== null ? (string) $phoneNumber : null;

        $request = clone $this;
        $request->phoneNumber = $phoneNumber;

        return $request;
    }

    /**
     * @param \Stringable|string $url
     *
     * @return self
     */
    public function withPhotoUrl($url) {
        $request = clone $this;
        $request->photoUrl = (string) CVendor_Firebase_Value_Url::fromValue((string) $url);

        return $request;
    }

    /**
     * @return self
     */
    public function markAsDisabled() {
        $request = clone $this;
        $request->markAsEnabled = null;
        $request->markAsDisabled = true;

        return $request;
    }

    /**
     * @return self
     */
    public function markAsEnabled() {
        $request = clone $this;
        $request->markAsDisabled = null;
        $request->markAsEnabled = true;

        return $request;
    }

    /**
     * @return self
     */
    public function markEmailAsVerified() {
        $request = clone $this;
        $request->emailIsVerified = true;

        return $request;
    }

    /**
     * @return self
     */
    public function markEmailAsUnverified() {
        $request = clone $this;
        $request->emailIsVerified = false;

        return $request;
    }

    /**
     * @param \Stringable|string $clearTextPassword
     *
     * @return self
     */
    public function withClearTextPassword($clearTextPassword) {
        $request = clone $this;
        $request->clearTextPassword = (string) (new CVendor_Firebase_Value_ClearTextPassword((string) $clearTextPassword));

        return $request;
    }

    /**
     * @return array<string, mixed>
     */
    public function prepareJsonSerialize() {
        $disableUser = null;
        if ($this->markAsDisabled) {
            $disableUser = true;
        } elseif ($this->markAsEnabled) {
            $disableUser = false;
        }

        return \array_filter([
            'localId' => $this->uid,
            'disableUser' => $disableUser,
            'displayName' => $this->displayName,
            'email' => $this->email,
            'emailVerified' => $this->emailIsVerified,
            'phoneNumber' => $this->phoneNumber,
            'photoUrl' => $this->photoUrl,
            'password' => $this->clearTextPassword,
        ], static fn ($value) => $value !== null);
    }

    /**
     * @return bool
     */
    public function hasUid() {
        return (bool) $this->uid;
    }
}
