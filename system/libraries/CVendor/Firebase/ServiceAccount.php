<?php

/**
 * @internal
 */
final class CVendor_Firebase_ServiceAccount {
    /**
     * @var array{
     *     project_id?: string,
     *     client_email?: string,
     *     private_key?: string,
     *     type: string
     * }
     */
    private $data;

    /**
     * @phpstan-param array{
     *     project_id?: string,
     *     client_email?: string,
     *     private_key?: string,
     *     type: string
     * } $data
     */
    private function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getProjectId() {
        return isset($this->data['project_id']) ? $this->data['project_id'] : '';
    }

    /**
     * @return string
     */
    public function getClientEmail() {
        return isset($this->data['client_email']) ? $this->data['client_email'] : '';
    }

    /**
     * @return string
     */
    public function getPrivateKey() {
        return isset($this->data['private_key']) ? $this->data['private_key'] : '';
    }

    /**
     * @return array{
     *     project_id?: string,
     *     client_email?: string,
     *     private_key?: string,
     *     type: string
     * }
     */
    public function asArray() {
        return $this->data;
    }

    /**
     * @param self|string|array|mixed $value
     *
     * @throws CVendor_Firebase_Exception_InvalidArgumentException
     *
     * @return self
     */
    public static function fromValue($value) {
        if ($value instanceof self) {
            return $value;
        }

        if (\is_string($value)) {
            try {
                if (\cstr::startsWith($value, '{')) {
                    return self::fromJson($value);
                }

                return self::fromJsonFile($value);
            } catch (Throwable $e) {
                throw new CVendor_Firebase_Exception_InvalidArgumentException('Invalid service account: ' . $e->getMessage(), $e->getCode(), $e);
            }
        }

        if (\is_array($value)) {
            try {
                return self::fromArray($value);
            } catch (Throwable $e) {
                throw new CVendor_Firebase_Exception_InvalidArgumentException('Invalid service account: ' . $e->getMessage(), $e->getCode(), $e);
            }
        }

        throw new CVendor_Firebase_Exception_InvalidArgumentException('Invalid service account: Unsupported value');
    }

    /**
     * @param array<string, string> $data
     *
     * @return self
     */
    private static function fromArray(array $data) {
        if (!\array_key_exists('type', $data) || $data['type'] !== 'service_account') {
            throw new CVendor_Firebase_Exception_InvalidArgumentException(
                'A Service Account specification must have a field "type" with "service_account" as its value.'
                . ' Please make sure you download the Service Account JSON file from the Service Accounts tab'
                . ' in the Firebase Console, as shown in the documentation on'
                . ' https://firebase.google.com/docs/admin/setup#add_firebase_to_your_app'
            );
        }

        return new self($data);
    }

    /**
     * @param string $json
     *
     * @return self
     */
    private static function fromJson($json) {
        $config = CVendor_Firebase_Util_JSON::decode($json, true);

        return self::fromArray($config);
    }

    /**
     * @param string $filePath
     *
     * @return self
     */
    private static function fromJsonFile($filePath) {
        try {
            $file = new \SplFileObject($filePath);
            $json = (string) $file->fread($file->getSize());
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException("{$filePath} can not be read: {$e->getMessage()}");
        }

        try {
            $serviceAccount = self::fromJson($json);
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException(\sprintf('%s could not be parsed to a Service Account: %s', $filePath, $e->getMessage()));
        }

        return $serviceAccount;
    }
}
