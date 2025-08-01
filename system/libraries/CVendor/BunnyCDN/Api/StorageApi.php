<?php

use GuzzleHttp\Exception\GuzzleException;

class CVendor_BunnyCDN_Api_StorageApi extends CVendor_BunnyCDN_ApiAbstract {
    public function __construct(CVendor_BunnyCDN_Client_StorageClient $client) {
        $this->client = $client;
    }

    /**
     * @param string $path
     *
     * @throws CVendor_BunnyCDN_Exception_NotFoundException|CVendor_BunnyCDN_Exception
     *
     * @return array
     */
    public function list($path) {
        try {
            $listing = $this->client->request(CVendor_BunnyCDN_Util::normalizePath($path) . '/');

            # Throw an exception if we don't get back an array
            if (!is_array($listing)) {
                throw new CVendor_BunnyCDN_Exception_NotFoundException('File is not a directory');
            }

            return array_map(function ($bunny_cdn_item) {
                return $bunny_cdn_item;
            }, $listing);
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

    /**
     * @param string $path
     *
     * @throws CVendor_BunnyCDN_Exception
     * @throws CVendor_BunnyCDN_Exception_NotFoundException
     *
     * @return mixed
     */
    public function download($path) {
        try {
            return $this->client->request($path . '?download');
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

    /**
     * @param string $path
     *
     * @throws CVendor_BunnyCDN_Exception
     * @throws CVendor_BunnyCDN_Exception_NotFoundException
     *
     * @return null|resource
     */
    public function stream($path) {
        $client = $this->client();
        /** @var CVendor_BunnyCDN_Client_StorageClient $client */
        return $client->stream($path);
    }

    /**
     * @param string $path
     * @param $contents
     *
     * @throws CVendor_BunnyCDN_Exception
     *
     * @return mixed
     */
    public function upload($path, $contents) {
        try {
            return $this->client->request($path, 'PUT', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                ],
                'body' => $contents
            ]);
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            throw new CVendor_BunnyCDN_Exception($e->getMessage());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     *
     * @throws CVendor_BunnyCDN_Exception
     *
     * @return mixed
     */
    public function makeDirectory($path) {
        try {
            return $this->client->request(CVendor_BunnyCDN_Util::normalizePath($path) . '/', 'PUT', [
                'headers' => [
                    'Content-Length' => 0
                ],
            ]);
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            if ($e->getCode() == 400) {
                throw new CVendor_BunnyCDN_Exception('Directory already exists');
            } else {
                throw new CVendor_BunnyCDN_Exception($e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     *
     * @throws CVendor_BunnyCDN_Exception_NotFoundException
     * @throws CVendor_BunnyCDN_Exception_DirectoryNotEmptyException
     * @throws CVendor_BunnyCDN_Exception
     *
     * @return mixed
     */
    public function delete($path) {
        try {
            return $this->client()->request($path, 'DELETE');
            // @codeCoverageIgnoreStart
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new CVendor_BunnyCDN_Exception_NotFoundException($e->getMessage());
            } elseif ($e->getCode() == 400) {
                throw new CVendor_BunnyCDN_Exception_DirectoryNotEmptyException($e->getMessage());
            } else {
                throw new CVendor_BunnyCDN_Exception($e->getMessage());
            }
        }
        // @codeCoverageIgnoreEnd
    }
}
