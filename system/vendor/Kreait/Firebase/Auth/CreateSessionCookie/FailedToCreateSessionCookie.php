<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\CreateSessionCookie;

use Throwable;
use Beste\Json;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Auth\CreateSessionCookie;
use Kreait\Firebase\Exception\RuntimeException;

final class FailedToCreateSessionCookie extends RuntimeException implements AuthException {
    private CreateSessionCookie $action;

    private ?ResponseInterface $response;

    public function __construct(
        CreateSessionCookie $action,
        ?ResponseInterface $response,
        ?string $message = null,
        ?int $code = null,
        ?Throwable $previous = null
    ) {
        $this->action = $action;
        $this->response = $response;
        $message ??= '';
        $code ??= 0;

        parent::__construct($message, $code, $previous);
    }

    public static function withActionAndResponse(CreateSessionCookie $action, ResponseInterface $response): self {
        $fallbackMessage = 'Failed to create session cookie';

        try {
            $message = Json::decode((string) $response->getBody(), true)['error']['message'] ?? $fallbackMessage;
        } catch (InvalidArgumentException $e) {
            $message = $fallbackMessage;
        }

        return new self($action, $response, $message);
    }

    public function action(): CreateSessionCookie {
        return $this->action;
    }

    public function response(): ?ResponseInterface {
        return $this->response;
    }
}
