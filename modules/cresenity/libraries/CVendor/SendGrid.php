<?php

class CVendor_SendGrid {
    const VERSION = '7.4.1';

    // @var string
    protected $namespace = 'SendGrid';
    // @var CVendor_SendGrid_Client
    public $client;
    // @var string
    public $version = self::VERSION;

    /**
     * Setup the HTTP Client
     *
     * @param string $apiKey  Your Twilio SendGrid API Key.
     * @param array  $options An array of options, currently only "host", "curl" and
     *                        "impersonateSubuser" are implemented.
     */
    public function __construct($apiKey, $options = []) {
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'User-Agent: sendgrid/' . $this->version . ';php',
            'Accept: application/json'
        ];
        $host = isset($options['host']) ? $options['host']
                : 'https://api.sendgrid.com';
        if (!empty($options['impersonateSubuser'])) {
            $headers[] = 'On-Behalf-Of: ' . $options['impersonateSubuser'];
        }
        $curlOptions = isset($options['curl']) ? $options['curl'] : null;
        $this->client = new CVendor_SendGrid_Client(
            $host,
            $headers,
            '/v3',
            null,
            $curlOptions
        );
    }

    /**
     * Make an API request
     *
     * @param CVendor_SendGrid_Mail_Mail $email A Mail object, containing the request object
     *
     * @return CVendor_SendGrid_Response
     */
    public function send(CVendor_SendGrid_Mail_Mail $email) {
        return $this->client->mail()->send()->post($email);
    }
}
