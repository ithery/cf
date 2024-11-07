<?php

use Psr\Http\Client\ClientInterface as HttpClient;

final class CVendor_Qontak_ClientFactory {
    /**
     * @var null|CVendor_Qontak_Client
     */
    private static $client = null;

    /**
     * @var null|CVendor_Qontak_NullClient
     */
    private static $nullClient = null;

    public static function makeFromArray(array $config, HttpClient $httpClient = null): CVendor_Qontak_ClientInterface {
        if (!self::$client instanceof CVendor_Qontak_Client) {
            self::$client = new CVendor_Qontak_Client(
                CVendor_Qontak_Credential::fromArray($config),
                $httpClient
            );
        }

        return self::$client;
    }

    public static function makeTestingClient(): CVendor_Qontak_ClientInterface {
        if (!self::$nullClient instanceof CVendor_Qontak_NullClient) {
            self::$nullClient = new CVendor_Qontak_NullClient();
        }

        return self::$nullClient;
    }
}
