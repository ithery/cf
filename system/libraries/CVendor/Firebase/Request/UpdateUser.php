<?php

final class CVendor_Firebase_Request_UpdateUser implements CVendor_Firebase_RequestInterface {
    use CVendor_Firebase_Request_Trait_EditUserTrait;
    const DISPLAY_NAME = 'DISPLAY_NAME';

    const PHOTO_URL = 'PHOTO_URL';

    const EMAIL = 'EMAIL';

    /**
     * @var array<string>
     */
    private $attributesToDelete = [];

    /**
     * @var string[]
     */
    private $providersToDelete = [];

    /**
     * @var null|array<string, mixed>
     */
    private $customAttributes = null;

    private function __construct() {
    }

    /**
     * @return self
     */
    public static function new() {
        return new self();
    }

    /**
     * @param array<string, mixed> $properties
     *
     * @throws CVendor_Firebase_Exception_InvalidArgumentException when invalid properties have been provided
     */
    public static function withProperties(array $properties): self {
        $request = self::withEditableProperties(new self(), $properties);
        /** @var $this $request */
        foreach ($properties as $key => $value) {
            switch (\mb_strtolower((string) \preg_replace('/[^a-z]/i', '', $key))) {
                case 'deletephoto':
                case 'deletephotourl':
                case 'removephoto':
                case 'removephotourl':
                    $request = $request->withRemovedPhotoUrl();

                    break;
                case 'deletedisplayname':
                case 'removedisplayname':
                    $request = $request->withRemovedDisplayName();

                    break;
                case 'deleteemail':
                case 'removeemail':
                    $request = $request->withRemovedEmail();

                    break;
                case 'deleteattribute':
                case 'deleteattributes':
                    foreach ((array) $value as $deleteAttribute) {
                        switch (\mb_strtolower(\preg_replace('/[^a-z]/i', '', $deleteAttribute))) {
                            case 'displayname':
                                $request = $request->withRemovedDisplayName();

                                break;
                            case 'photo':
                            case 'photourl':
                                $request = $request->withRemovedPhotoUrl();

                                break;
                            case 'email':
                                $request = $request->withRemovedEmail();

                                break;
                        }
                    }

                    break;
                case 'customattributes':
                case 'customclaims':
                    $request = $request->withCustomAttributes($value);

                    break;
                case 'phonenumber':
                case 'phone':
                    if (!$value) {
                        $request = $request->withRemovedPhoneNumber();
                    }

                    break;
                case 'deletephone':
                case 'deletephonenumber':
                case 'removephone':
                case 'removephonenumber':
                    $request = $request->withRemovedPhoneNumber();

                    break;
                case 'deleteprovider':
                case 'deleteproviders':
                case 'removeprovider':
                case 'removeproviders':
                    $request = \array_reduce(
                        (array) $value,
                        static fn (self $request, $provider) => $request->withRemovedProvider($provider),
                        $request
                    );

                    break;
            }
        }

        return $request;
    }

    public function withRemovedPhoneNumber(): self {
        $request = clone $this;
        $request->phoneNumber = null;

        return $request->withRemovedProvider('phone');
    }

    /**
     * @param \Stringable|string $provider
     */
    public function withRemovedProvider($provider): self {
        $request = clone $this;
        $request->providersToDelete[] = (string) $provider;

        return $request;
    }

    public function withRemovedDisplayName(): self {
        $request = clone $this;
        $request->displayName = null;
        $request->attributesToDelete[] = self::DISPLAY_NAME;

        return $request;
    }

    /**
     * @return self
     */
    public function withRemovedPhotoUrl() {
        $request = clone $this;
        $request->photoUrl = null;
        $request->attributesToDelete[] = self::PHOTO_URL;

        return $request;
    }

    /**
     * @return self
     */
    public function withRemovedEmail() {
        $request = clone $this;
        $request->email = null;
        $request->attributesToDelete[] = self::EMAIL;

        return $request;
    }

    /**
     * @param array<string, mixed> $customAttributes
     *
     * @return self
     */
    public function withCustomAttributes(array $customAttributes) {
        $request = clone $this;
        $request->customAttributes = $customAttributes;

        return $request;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() {
        if (!$this->hasUid()) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('A uid is required to update an existing user.');
        }

        $data = $this->prepareJsonSerialize();

        if (\is_array($this->customAttributes)) {
            if (empty($this->customAttributes)) {
                $data['customAttributes'] = '{}';
            } else {
                $data['customAttributes'] = CVendor_Firebase_Util_JSON::encode($this->customAttributes);
            }
        }

        if (!empty($this->attributesToDelete)) {
            $data['deleteAttribute'] = \array_unique($this->attributesToDelete);
        }

        if (!empty($this->providersToDelete)) {
            $data['deleteProvider'] = $this->providersToDelete;
        }

        return $data;
    }
}
