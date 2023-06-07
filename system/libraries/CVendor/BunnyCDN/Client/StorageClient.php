<?php
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;

class CVendor_BunnyCDN_Client_StorageClient extends CVendor_BunnyCDN_ClientAbstract {
    public $storageZoneName;

    private $apiKey;

    private $region;

    public function __construct($storageZoneName, $apiKey, $region = CVendor_BunnyCDN_Region::FALKENSTEIN) {
        $this->storageZoneName = $storageZoneName;
        $this->apiKey = $apiKey;
        $this->region = $region;
        if ($this->region === null) {
            throw new Exception('Please specify region for BunnyCDN');
        }
    }

    public function getStorageZoneName() {
        return $this->storageZoneName;
    }

    public function getBaseUrl() {
        $mapRegionUrl = [
            CVendor_BunnyCDN_Region::NEW_YORK => 'https://ny.storage.bunnycdn.com/',
            CVendor_BunnyCDN_Region::LOS_ANGELES => 'https://la.storage.bunnycdn.com/',
            CVendor_BunnyCDN_Region::SINGAPORE => 'https://sg.storage.bunnycdn.com/',
            CVendor_BunnyCDN_Region::SYDNEY => 'https://syd.storage.bunnycdn.com/',
            CVendor_BunnyCDN_Region::UNITED_KINGDOM => 'https://uk.storage.bunnycdn.com/',
            CVendor_BunnyCDN_Region::STOCKHOLM => 'https://se.storage.bunnycdn.com/',

        ];
        $defaultRegionUrl = 'https://storage.bunnycdn.com/';

        return carr::get($mapRegionUrl, $this->region, $defaultRegionUrl);
    }

    /**
     * @param mixed $path
     * @param mixed $method
     *
     * @throws GuzzleException
     */
    public function request($path, $method = 'GET', array $options = []) {
        $response = $this->guzzleClient()->request(
            $method,
            $this->getBaseUrl() . CVendor_BunnyCDN_Util::normalizePath('/' . $this->storageZoneName . '/') . $path,
            array_merge_recursive([
                'headers' => [
                    'Accept' => '*/*',
                    'AccessKey' => $this->apiKey, # Honestly... Why do I have to specify this twice... @BunnyCDN
                ],
            ], $options)
        );

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true) ?? $contents;
    }

    /**
     * @param string $path
     *
     * @throws CVendor_BunnyCDN_Exception
     * @throws CVendor_BunnyCDN_Exception_NotFoundException
     *
     * @return null|resource
     */
    public function stream($path) {
        try {
            return $this->client->request(
                'GET',
                $this->getBaseUrl() . CVendor_BunnyCDN_Util::normalizePath('/' . $this->storageZoneName . '/') . $path,
                array_merge_recursive([
                    'stream' => true,
                    'headers' => [
                        'Accept' => '*/*',
                        'AccessKey' => $this->apiKey, # Honestly... Why do I have to specify this twice... @BunnyCDN
                    ]
                ])
            )->getBody()->detach();
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new CVendor_BunnyCDN_Exception_NotFoundException($e->getMessage());
            } else {
                throw new CVendor_BunnyCDN_Exception($e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }
}
