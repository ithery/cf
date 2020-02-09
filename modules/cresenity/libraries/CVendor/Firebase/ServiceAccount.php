<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CVendor_Firebase_ServiceAccount {

    private $projectId;
    private $sanitizedProjectId;
    private $clientId;
    private $clientEmail;
    private $privateKey;

    /** @var string|null */
    private $filePath;

    /**
     * @return string|null
     */
    public function getFilePath() {
        return $this->filePath;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    public function getSanitizedProjectId() {
        if (!$this->sanitizedProjectId) {
            $this->sanitizedProjectId = \preg_replace('/[^A-Za-z0-9\-]/', '-', $this->projectId);
        }

        return $this->sanitizedProjectId;
    }

    public function withProjectId($value) {
        $serviceAccount = clone $this;
        $serviceAccount->projectId = $value;
        $serviceAccount->sanitizedProjectId = null;

        return $serviceAccount;
    }

    public function hasClientId() {
        return (bool) $this->clientId;
    }

    public function getClientId() {
        return $this->clientId;
    }

    public function withClientId($value) {
        $serviceAccount = clone $this;
        $serviceAccount->clientId = $value;

        return $serviceAccount;
    }

    public function getClientEmail() {
        return $this->clientEmail;
    }

    public function withClientEmail($value) {
        if (!\filter_var($value, \FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(\sprintf('"%s" is not a valid email.', $value));
        }
        $serviceAccount = clone $this;
        $serviceAccount->clientEmail = $value;

        return $serviceAccount;
    }

    public function hasPrivateKey() {
        return (bool) $this->privateKey;
    }

    public function getPrivateKey() {
        return $this->privateKey;
    }

    public function withPrivateKey($value) {
        $serviceAccount = clone $this;
        $serviceAccount->privateKey = \str_replace('\n', "\n", $value);

        return $serviceAccount;
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     *
     * @return ServiceAccount
     */
    public static function fromValue($value) {
        if ($value instanceof self) {
            return $value;
        }

        if (\is_string($value) && \mb_strpos($value, '{') === 0) {
            try {
                return self::fromJson($value);
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException('Invalid service account specification');
            }
        }

        if (\is_string($value) && \mb_strpos($value, '{') !== 0) {
            try {
                return self::fromJsonFile($value);
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException('Invalid service account specification');
            }
        }

        if (\is_array($value)) {
            return self::fromArray($value);
        }

        throw new InvalidArgumentException('Invalid service account specification.');
    }

    public static function fromArray(array $config) {
        $requiredFields = ['project_id', 'client_id', 'client_email', 'private_key'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($config[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            throw new InvalidArgumentException(
            'The following fields are missing/empty in the Service Account specification: "'
            . \implode('", "', $missingFields)
            . '". Please make sure you download the Service Account JSON file from the Service Accounts tab '
            . 'in the Firebase Console, as shown in the documentation on '
            . 'https://firebase.google.com/docs/admin/setup#add_firebase_to_your_app'
            );
        }

        return (new self())
                        ->withProjectId($config['project_id'])
                        ->withClientId($config['client_id'])
                        ->withClientEmail($config['client_email'])
                        ->withPrivateKey($config['private_key']);
    }

    public static function fromJson($json) {
        $config = CHelper::json()->decode($json, true);

        return self::fromArray($config);
    }

    public static function fromJsonFile($filePath) {
        try {
            $file = new \SplFileObject($filePath);
            $json = $file->fread($file->getSize());
        } catch (Throwable $e) {
            throw new InvalidArgumentException("{$filePath} can not be read: {$e->getMessage()}");
        }

        if (!\is_string($json)) {
            throw new InvalidArgumentException("{$filePath} can not be read");
        }

        try {
            $serviceAccount = self::fromJson($json);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(\sprintf('%s could not be parsed to a Service Account: %s', $filePath, $e->getMessage()));
        }

        $serviceAccount->filePath = $filePath;

        return $serviceAccount;
    }

    public static function withProjectIdAndServiceAccountId($projectId, $serviceAccountId) {
        $serviceAccount = new self();
        $serviceAccount->projectId = $projectId;
        $serviceAccount->clientEmail = $serviceAccountId;

        return $serviceAccount;
    }

    /**
     * @return ServiceAccount
     */
    public static function discover(CVendor_Firebase_ServiceAccount_Discoverer $discoverer = null) {
        $discoverer = $discoverer ?: new CVendor_Firebase_ServiceAccount_Discoverer();

        return $discoverer->discover();
    }

}
