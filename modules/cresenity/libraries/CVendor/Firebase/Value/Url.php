<?php
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
final class CVendor_Firebase_Value_Url implements \JsonSerializable {
    /**
     * @var UriInterface
     */
    public function __construct(UriInterface $value) {
        $this->value = $value;
    }

    /**
     * @param \Stringable|string $value
     *
     * @throws CVendor_Firebase_Exception_InvalidArgumentException
     */
    public static function fromValue($value): self {
        if ($value instanceof UriInterface) {
            return new self($value);
        }

        try {
            return new self(new Uri((string) $value));
        } catch (Throwable $e) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * @return UriInterface
     */
    public function toUri() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string) $this->value;
    }

    /**
     * @return string
     */
    public function jsonSerialize() {
        return (string) $this->value;
    }

    /**
     * @param \Stringable|string $other
     *
     * @return bool
     */
    public function equalsTo($other) {
        return (string) $this->value === (string) $other;
    }
}
