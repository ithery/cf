<?php

/**
 * @method mixed getAppData()
 * @method mixed getChecksum()
 * @method mixed getConfig()
 * @method mixed getError()
 * @method bool isAppData()
 * @method bool isChecksum()
 * @method bool isConfig()
 * @method bool isError()
 * @method setAppData(mixed $value)
 * @method setChecksum(mixed $value)
 * @method setConfig(mixed $value)
 * @method setError(mixed $value)
 */
class InstagramAPI_Response_ClientEventLogsResponse extends InstagramAPI_AutoPropertyHandler implements InstagramAPI_ResponseInterface {

    use InstagramAPI_ResponseTrait;

    public $checksum;
    public $config;
    public $app_data;
    public $error;

}
