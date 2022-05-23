<?php
final class CVendor_Firebase_JWT_Token implements CVendor_Firebase_JWT_Contract_TokenInterface {
    private $encodedString;

    /**
     * @var array<string, mixed>
     */
    private $headers;

    /**
     * @var array<string, mixed>
     */
    private $payload;

    /**
     * @param array<string, mixed> $headers
     * @param array<string, mixed> $payload
     * @param mixed                $encodedString
     */
    private function __construct($encodedString, array $headers, array $payload) {
        $this->encodedString = $encodedString;
        $this->headers = $headers;
        $this->payload = $payload;
    }

    /**
     * @param array<string, mixed> $headers
     * @param array<string, mixed> $payload
     * @param string               $encodedString
     */
    public static function withValues($encodedString, array $headers, array $payload) {
        return new self($encodedString, $headers, $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function headers() {
        return $this->headers;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload() {
        return $this->payload;
    }

    public function toString() {
        return $this->encodedString;
    }

    public function __toString() {
        return $this->toString();
    }
}
