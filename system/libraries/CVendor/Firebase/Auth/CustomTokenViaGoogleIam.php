<?php

use Lcobucci\JWT\Token;
use GuzzleHttp\ClientInterface;
use Lcobucci\JWT\Configuration;

/**
 * @internal
 */
final class CVendor_Firebase_Auth_CustomTokenViaGoogleIam {
    /**
     * @var string
     */
    private $clientEmail;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var null|string
     */
    private $tenantId;

    /**
     * @param string          $clientEmail
     * @param ClientInterface $client
     * @param null|string     $tenantId
     */
    public function __construct($clientEmail, ClientInterface $client, $tenantId = null) {
        $this->clientEmail = $clientEmail;
        $this->client = $client;
        $this->tenantId = $tenantId;

        $this->config = Configuration::forUnsecuredSigner();
    }

    /**
     * @param \Stringable|string$uid
     * @param array<string, mixed> $claims
     *
     * @throws CVendor_Firebase_Auth_ExceptionInterface
     * @throws CVendor_Firebase_ExceptionInterface
     *
     * @return Token
     */
    public function createCustomToken($uid, array $claims = [], ?DateTimeInterface $expiresAt = null) {
        $now = new \DateTimeImmutable();
        $expiresAt = ($expiresAt !== null)
            ? CVendor_Firebase_Util_DT::toUTCDateTimeImmutable($expiresAt)
            : $now->add(new \DateInterval('PT1H'));

        $builder = $this->config->builder()
            ->withClaim('uid', (string) $uid)
            ->issuedBy($this->clientEmail)
            ->permittedFor('https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit')
            ->relatedTo($this->clientEmail)
            ->issuedAt($now)
            ->expiresAt($expiresAt);

        if ($this->tenantId !== null) {
            $builder->withClaim('tenantId', $this->tenantId);
        }

        if (!empty($claims)) {
            $builder->withClaim('claims', $claims);
        }

        $token = $builder->getToken($this->config->signer(), $this->config->signingKey());

        $url = 'https://iam.googleapis.com/v1/projects/-/serviceAccounts/' . $this->clientEmail . ':signBlob';

        try {
            $response = $this->client->request('POST', $url, [
                'json' => [
                    'bytesToSign' => \base64_encode($token->payload()),
                ],
            ]);
        } catch (Throwable $e) {
            throw (new CVendor_Firebase_Auth_ApiExceptionConverter())->convertException($e);
        }

        $result = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true);

        if ($base64EncodedSignature = $result['signature'] ?? null) {
            try {
                return $this->config->parser()->parse($token->payload() . '.' . $base64EncodedSignature);
            } catch (InvalidArgumentException $e) {
                throw new CVendor_Firebase_Auth_Exception_AuthErrorException('The custom token API returned an unexpected value: ' . $e->getMessage(), $e->getCode(), $e);
            }
        }

        throw new CVendor_Firebase_Auth_Exception_AuthErrorException('Unable to create custom token.');
    }
}
