<?php

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class CApi_OAuth_Bridge_ClientRepository implements ClientRepositoryInterface {
    /**
     * The client model repository.
     *
     * @var \CApi_OAuth_ClientRepository
     */
    protected $clients;

    /**
     * Create a new repository instance.
     *
     * @param \CApi_OAuth_ClientRepository $clients
     *
     * @return void
     */
    public function __construct(CApi_OAuth_ClientRepository $clients) {
        $this->clients = $clients;
    }

    /**
     * @inheritdoc
     */
    public function getClientEntity($clientIdentifier) {
        $record = $this->clients->findActive($clientIdentifier);

        if (!$record) {
            return;
        }

        return new CApi_OAuth_Bridge_Client(
            $clientIdentifier,
            $record->name,
            $record->redirect,
            $record->confidential(),
            $record->provider
        );
    }

    /**
     * @inheritdoc
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType) {
        // First, we will verify that the client exists and is authorized to create personal
        // access tokens. Generally personal access tokens are only generated by the user
        // from the main interface. We'll only let certain clients generate the tokens.
        $record = $this->clients->findActive($clientIdentifier);

        if (!$record || !$this->handlesGrant($record, $grantType)) {
            return false;
        }

        return !$record->confidential() || $this->verifySecret((string) $clientSecret, $record->secret);
    }

    /**
     * Determine if the given client can handle the given grant type.
     *
     * @param \CApi_OAuth_Model_OAuthClient $record
     * @param string                        $grantType
     *
     * @return bool
     */
    protected function handlesGrant($record, $grantType) {
        if (is_array($record->grant_types) && !in_array($grantType, $record->grant_types)) {
            return false;
        }

        switch ($grantType) {
            case 'authorization_code':
                return !$record->firstParty();
            case 'personal_access':
                return $record->personal_access_client && $record->confidential();
            case 'password':
                return $record->password_client;
            case 'client_credentials':
                return $record->confidential();
            default:
                return true;
        }
    }

    /**
     * Verify the client secret is valid.
     *
     * @param string $clientSecret
     * @param string $storedHash
     *
     * @return bool
     */
    protected function verifySecret($clientSecret, $storedHash) {
        return CApi::oauth()->hashesClientSecrets
            ? password_verify($clientSecret, $storedHash)
            : hash_equals($storedHash, $clientSecret);
    }
}
