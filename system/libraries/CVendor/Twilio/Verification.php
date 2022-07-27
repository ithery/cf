<?php
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class CVendor_Twilio_Verification {
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $verificationSid;

    /**
     * Verification constructor.
     *
     * @param $client
     * @param null|string $verificationSid
     *
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function __construct($client, string $verificationSid = null) {
        $this->client = $client;
        $this->verificationSid = $verificationSid;
    }

    /**
     * Start a phone verification process using Twilio Verify V2 API.
     *
     * @param $phoneNumber
     * @param $channel
     *
     * @return CVendor_Twilio_Verification_Result
     */
    public function startVerification($phoneNumber, $channel) {
        try {
            $verification = $this->client->verify->v2->services($this->verificationSid)
                ->verifications
                ->create($phoneNumber, $channel);

            return new CVendor_Twilio_Verification_Result($verification->sid);
        } catch (TwilioException $exception) {
            throw new CVendor_Twilio_Exception($exception->getMessage());

            //return new Exception(["Verification failed to start: {$exception->getMessage()}"]);
        }
    }

    /**
     * Check verification code using Twilio Verify V2 API.
     *
     * @param $phoneNumber
     * @param $code
     *
     * @return CVendor_Twilio_Verification_Result
     */
    public function checkVerification($phoneNumber, $code) {
        try {
            $verificationCheck = $this->client->verify->v2->services($this->verificationSid)
                ->verificationChecks
                ->create($code, ['to' => $phoneNumber]);
            if ($verificationCheck->status === 'approved') {
                return new CVendor_Twilio_Verification_Result($verificationCheck->sid);
            }

            throw new CVendor_Twilio_Exception_InvalidCodeException('Verification check failed: Invalid code.');
            //return new CVendor_Twilio_Verification_Result(['Verification check failed: Invalid code.']);
        } catch (TwilioException $exception) {
            throw new CVendor_Twilio_Exception("Verification check failed: {$exception->getMessage()}");
            //return new CVendor_Twilio_Verification_Result(["Verification check failed: {$exception->getMessage()}"]);
        }
    }
}
