<?php

use Twilio\Rest\Client;

class CVendor_Twilio {
    protected $sid;

    protected $token;

    protected $client;

    public function __construct($sid, $token) {
        $this->sid = $sid;
        $this->token = $token;
    }

    /**
     * Get Twilio Client.
     *
     * @return \Twilio\Rest\Client
     */
    public function client() {
        if ($this->client == null) {
            $this->client = new Client($this->sid, $this->token);
        }

        return $this->client;
    }

    public function verification($verificationSid) {
        return new CVendor_Twilio_Verification($this->client(), $verificationSid);
    }
}
