<?php

class CVendor_BunnyCDN_Client_StreamClient extends CVendor_BunnyCDN_ClientAbstract {
    private $streamAccessKey;

    public function __construct($streamAccessKey) {
        $this->streamAccessKey = $streamAccessKey;
        if ($this->streamAccessKey === null) {
            throw new Exception('Please specify streamAccessKey for BunnyCDN Stream');
        }
    }

    public function getBaseUrl() {
        return 'https://video.bunnycdn.com/';
    }

    /**
     * @param mixed $path
     * @param mixed $method
     *
     * @throws GuzzleException
     *
     * @see CStorage_Vendor_BunnyCDN_Client
     */
    public function request($path, $method = 'GET', array $options = []) {
        $response = $this->guzzleClient()->request(
            $method,
            $this->getBaseUrl() . $path,
            array_merge_recursive([
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/*+json',
                    'AccessKey' => $this->streamAccessKey,
                ],
            ], $options)
        );

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true) ?? $contents;
    }
}
