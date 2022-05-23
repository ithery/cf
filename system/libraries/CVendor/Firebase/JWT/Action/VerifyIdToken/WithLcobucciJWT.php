<?php

use Psr\Clock\ClockInterface;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

/**
 * @internal
 */
final class CVendor_Firebase_JWT_Action_VerifyIdToken_WithLcobucciJWT implements CVendor_Firebase_JWT_Action_VerifyIdToken_HandlerInterface {
    /**
     * @var string
     */
    private $projectId;

    /**
     * @var CVendor_Firebase_JWT_Contract_KeysInterface
     */
    private $keys;

    /**
     * @var ClockInterface
     */
    private $clock;

    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * @param string                                      $projectId
     * @param CVendor_Firebase_JWT_Contract_KeysInterface $keys
     * @param ClockInterface                              $clock
     */
    public function __construct(string $projectId, CVendor_Firebase_JWT_Contract_KeysInterface $keys, ClockInterface $clock) {
        $this->projectId = $projectId;
        $this->keys = $keys;
        $this->clock = $clock;

        $this->config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText(''));
    }

    /**
     * @param CVendor_Firebase_JWT_Action_VerifyIdToken $action
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function handle(CVendor_Firebase_JWT_Action_VerifyIdToken $action) {
        $tokenString = $action->token();

        try {
            $token = $this->config->parser()->parse($tokenString);
            \assert($token instanceof UnencryptedToken);
        } catch (Throwable $e) {
            throw CVendor_Firebase_JWT_Exception_IdTokenVerificationFailedException::withTokenAndReasons($tokenString, ['The token is invalid', $e->getMessage()]);
        }

        $key = $this->getKey($token);
        $clock = new FrozenClock($this->clock->now());
        $leeway = new DateInterval('PT' . $action->leewayInSeconds() . 'S');
        $errors = [];

        try {
            $this->config->validator()->assert(
                $token,
                new LooseValidAt($clock, $leeway),
                new IssuedBy(...["https://securetoken.google.com/{$this->projectId}"]),
                new PermittedFor($this->projectId),
                new SignedWith(
                    $this->config->signer(),
                    InMemory::plainText($key)
                )
            );

            $this->assertUserAuthedAt($token, $clock->now()->add($leeway));

            if ($tenantId = $action->expectedTenantId()) {
                $this->assertTenantId($token, $tenantId);
            }
        } catch (RequiredConstraintsViolated $e) {
            $errors = \array_map(
                static fn (ConstraintViolation $violation): string => '- ' . $violation->getMessage(),
                $e->violations()
            );
        }

        if (!empty($errors)) {
            throw CVendor_Firebase_JWT_Exception_IdTokenVerificationFailedException::withTokenAndReasons($tokenString, $errors);
        }

        $claims = $token->claims()->all();

        foreach ($claims as &$claim) {
            if ($claim instanceof DateTimeInterface) {
                $claim = $claim->getTimestamp();
            }
        }
        unset($claim);

        $headers = $token->headers()->all();
        foreach ($headers as &$header) {
            if ($header instanceof DateTimeInterface) {
                $header = $header->getTimestamp();
            }
        }
        unset($header);

        return CVendor_Firebase_JWT_Token::withValues($tokenString, $headers, $claims);
    }

    /**
     * @param UnencryptedToken $token
     *
     * @return string
     */
    private function getKey(UnencryptedToken $token) {
        if (empty($keys = $this->keys->all())) {
            throw CVendor_Firebase_JWT_Exception_IdTokenVerificationFailedException::withTokenAndReasons($token->toString(), ["No keys are available to verify the token's signature."]);
        }

        $keyId = $token->headers()->get('kid');

        if ($key = $keys[$keyId] ?? null) {
            return $key;
        }

        throw CVendor_Firebase_JWT_Exception_IdTokenVerificationFailedException::withTokenAndReasons($token->toString(), ["No public key matching the key ID '{$keyId}' was found to verify the signature of this token."]);
    }

    private function assertUserAuthedAt(UnencryptedToken $token, DateTimeInterface $now): void {
        /** @var int|DateTimeImmutable $authTime */
        $authTime = $token->claims()->get('auth_time');

        if (!$authTime) {
            throw RequiredConstraintsViolated::fromViolations(
                new ConstraintViolation('The token is missing the "auth_time" claim.')
            );
        }

        if (\is_numeric($authTime)) {
            $authTime = new DateTimeImmutable('@' . ((int) $authTime));
        }

        if ($now < $authTime) {
            throw RequiredConstraintsViolated::fromViolations(
                new ConstraintViolation("The token's user must have authenticated in the past")
            );
        }
    }

    /**
     * @param UnencryptedToken $token
     * @param string           $tenantId
     *
     * @return void
     */
    private function assertTenantId(UnencryptedToken $token, $tenantId) {
        $claim = (array) $token->claims()->get('firebase', []);

        $tenant = $claim['tenant'] ?? null;

        if (!\is_string($tenant)) {
            throw RequiredConstraintsViolated::fromViolations(
                new ConstraintViolation('The ID token does not contain a tenant identifier')
            );
        }

        if ($tenant !== $tenantId) {
            throw RequiredConstraintsViolated::fromViolations(
                new ConstraintViolation("The token's tenant ID did not match with the expected tenant ID")
            );
        }
    }
}
