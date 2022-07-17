<?php

class CValidation_UncompromisedVerifier_NotPwnedVerifier implements CValidation_Contract_UncompromisedVerifierInterface {
    /**
     * The number of seconds the request can run before timing out.
     *
     * @var int
     */
    protected $timeout;

    /**
     * Create a new uncompromised verifier.
     *
     * @param null|int $timeout
     *
     * @return void
     */
    public function __construct($timeout = null) {
        $this->timeout = $timeout ?: 30;
    }

    /**
     * Verify that the given data has not been compromised in public breaches.
     *
     * @param array $data
     *
     * @return bool
     */
    public function verify($data) {
        $value = $data['value'];
        $threshold = $data['threshold'];

        if (empty($value = (string) $value)) {
            return false;
        }

        list($hash, $hashPrefix) = $this->getHash($value);

        return !$this->search($hashPrefix)
            ->contains(function ($line) use ($hash, $hashPrefix, $threshold) {
                list($hashSuffix, $count) = explode(':', $line);

                return $hashPrefix . $hashSuffix == $hash && $count > $threshold;
            });
    }

    /**
     * Get the hash and its first 5 chars.
     *
     * @param string $value
     *
     * @return array
     */
    protected function getHash($value) {
        $hash = strtoupper(sha1((string) $value));

        $hashPrefix = substr($hash, 0, 5);

        return [$hash, $hashPrefix];
    }

    /**
     * Search by the given hash prefix and returns all occurrences of leaked passwords.
     *
     * @param string $hashPrefix
     *
     * @return \CCollection
     */
    protected function search($hashPrefix) {
        try {
            $response = CHTTP::client()->withHeaders([
                'Add-Padding' => true,
            ])->timeout($this->timeout)->get(
                'https://api.pwnedpasswords.com/range/' . $hashPrefix
            );
        } catch (Exception $e) {
            c::report($e);
        }

        $body = (isset($response) && $response->successful())
            ? $response->body()
            : '';

        return cstr::of($body)->trim()->explode("\n")->filter(function ($line) {
            return str_contains($line, ':');
        });
    }
}
