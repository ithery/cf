<?php

class CVendor_Dropbox_InMemoryTokenProvider implements CVendor_Dropbox_TokenProviderInterface {
    protected $token;

    public function __construct(string $token) {
        $this->token = $token;
    }

    public function getToken(): string {
        return $this->token;
    }
}
