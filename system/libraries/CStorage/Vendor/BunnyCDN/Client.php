<?php
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;

class CStorage_Vendor_BunnyCDN_Client {
    public $storage_zone_name;

    public Guzzle $client;

    private $api_key;

    private $region;

    public function __construct($storage_zone_name, $api_key, $region = CStorage_Vendor_BunnyCDN_Region::FALKENSTEIN) {
        $this->storage_zone_name = $storage_zone_name;
        $this->api_key = $api_key;
        $this->region = $region;
        if ($this->region === null) {
            throw new Exception('Please specify region for BunnyCDN');
        }
        $this->client = new Guzzle();
    }

    private static function getBaseUrl($region) {
        $mapRegionUrl = [
            CStorage_Vendor_BunnyCDN_Region::NEW_YORK => 'https://ny.storage.bunnycdn.com/',
            CStorage_Vendor_BunnyCDN_Region::LOS_ANGELES => 'https://la.storage.bunnycdn.com/',
            CStorage_Vendor_BunnyCDN_Region::SINGAPORE => 'https://sg.storage.bunnycdn.com/',
            CStorage_Vendor_BunnyCDN_Region::SYDNEY => 'https://syd.storage.bunnycdn.com/',
            CStorage_Vendor_BunnyCDN_Region::UNITED_KINGDOM => 'https://uk.storage.bunnycdn.com/',
            CStorage_Vendor_BunnyCDN_Region::STOCKHOLM => 'https://se.storage.bunnycdn.com/',

        ];
        $defaultRegionUrl = 'https://storage.bunnycdn.com/';

        return carr::get($mapRegionUrl, $region, $defaultRegionUrl);
    }

    /**
     * @param mixed $path
     * @param mixed $method
     *
     * @throws GuzzleException
     */
    private function request($path, $method = 'GET', array $options = []) {
        $response = $this->client->request(
            $method,
            self::getBaseUrl($this->region) . CStorage_Vendor_BunnyCDN_Util::normalizePath('/' . $this->storage_zone_name . '/') . $path,
            array_merge_recursive([
                'headers' => [
                    'Accept' => '*/*',
                    'AccessKey' => $this->api_key, # Honestly... Why do I have to specify this twice... @BunnyCDN
                ],
            ], $options)
        );

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true) ?? $contents;
    }

    /**
     * @param string $path
     *
     * @throws CStorage_Vendor_BunnyCDN_Exception_NotFoundException|CStorage_Vendor_BunnyCDN_Exception
     *
     * @return array
     */
    public function list($path) {
        try {
            $listing = $this->request(CStorage_Vendor_BunnyCDN_Util::normalizePath($path) . '/');

            # Throw an exception if we don't get back an array
            if (!is_array($listing)) {
                throw new CStorage_Vendor_BunnyCDN_Exception_NotFoundException('File is not a directory');
            }

            return array_map(function ($bunny_cdn_item) {
                return $bunny_cdn_item;
            }, $listing);
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new CStorage_Vendor_BunnyCDN_Exception_NotFoundException($e->getMessage());
            } else {
                throw new CStorage_Vendor_BunnyCDN_Exception($e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     *
     * @throws CStorage_Vendor_BunnyCDN_Exception
     * @throws CStorage_Vendor_BunnyCDN_Exception_NotFoundException
     *
     * @return mixed
     */
    public function download($path) {
        try {
            return $this->request($path . '?download');
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new CStorage_Vendor_BunnyCDN_Exception_NotFoundException($e->getMessage());
            } else {
                throw new CStorage_Vendor_BunnyCDN_Exception($e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     *
     * @throws CStorage_Vendor_BunnyCDN_Exception
     * @throws CStorage_Vendor_BunnyCDN_Exception_NotFoundException
     *
     * @return null|resource
     */
    public function stream($path) {
        try {
            return $this->client->request(
                'GET',
                self::getBaseUrl($this->region) . CStorage_Vendor_BunnyCDN_Util::normalizePath('/' . $this->storage_zone_name . '/') . $path,
                array_merge_recursive([
                    'stream' => true,
                    'headers' => [
                        'Accept' => '*/*',
                        'AccessKey' => $this->api_key, # Honestly... Why do I have to specify this twice... @BunnyCDN
                    ]
                ])
            )->getBody()->detach();
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new CStorage_Vendor_BunnyCDN_Exception_NotFoundException($e->getMessage());
            } else {
                throw new CStorage_Vendor_BunnyCDN_Exception($e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     * @param $contents
     *
     * @throws CStorage_Vendor_BunnyCDN_Exception
     *
     * @return mixed
     */
    public function upload($path, $contents) {
        try {
            return $this->request($path, 'PUT', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                ],
                'body' => $contents
            ]);
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            throw new CStorage_Vendor_BunnyCDN_Exception($e->getMessage());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     *
     * @throws CStorage_Vendor_BunnyCDN_Exception
     *
     * @return mixed
     */
    public function makeDirectory($path) {
        try {
            return $this->request(CStorage_Vendor_BunnyCDN_Util::normalizePath($path) . '/', 'PUT', [
                'headers' => [
                    'Content-Length' => 0
                ],
            ]);
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            if ($e->getCode() == 400) {
                throw new CStorage_Vendor_BunnyCDN_Exception('Directory already exists');
            } else {
                throw new CStorage_Vendor_BunnyCDN_Exception($e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     *
     * @throws CStorage_Vendor_BunnyCDN_Exception_NotFoundException
     * @throws CStorage_Vendor_BunnyCDN_Exception_DirectoryNotEmptyException
     * @throws CStorage_Vendor_BunnyCDN_Exception
     *
     * @return mixed
     */
    public function delete(string $path) {
        try {
            return $this->request($path, 'DELETE');
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new CStorage_Vendor_BunnyCDN_Exception_NotFoundException($e->getMessage());
            } elseif ($e->getCode() == 400) {
                throw new CStorage_Vendor_BunnyCDN_Exception_DirectoryNotEmptyException($e->getMessage());
            } else {
                throw new CStorage_Vendor_BunnyCDN_Exception($e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }
}
