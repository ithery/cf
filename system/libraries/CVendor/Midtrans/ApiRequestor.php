<?php

/**
 * Send request to Midtrans API
 * Better don't use this class directly, please use CoreApi, Snap, and Transaction instead.
 */
class CVendor_Midtrans_ApiRequestor {
    /**
     * Send GET request.
     *
     * @param string  $url
     * @param string  $server_key
     * @param mixed[] $data_hash
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function get($url, $server_key, $data_hash) {
        return self::remoteCall($url, $server_key, $data_hash, 'GET');
    }

    /**
     * Send POST request.
     *
     * @param string  $url
     * @param string  $server_key
     * @param mixed[] $data_hash
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function post($url, $server_key, $data_hash) {
        return self::remoteCall($url, $server_key, $data_hash, 'POST');
    }

    /**
     * Send PATCH request.
     *
     * @param string  $url
     * @param string  $server_key
     * @param mixed[] $data_hash
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function patch($url, $server_key, $data_hash) {
        return self::remoteCall($url, $server_key, $data_hash, 'PATCH');
    }

    /**
     * Actually send request to API server.
     *
     * @param string  $url
     * @param string  $server_key
     * @param mixed[] $data_hash
     * @param mixed   $method
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function remoteCall($url, $server_key, $data_hash, $method) {
        $ch = curl_init();

        if (!$server_key) {
            throw new Exception(
                'The ServerKey/ClientKey is null, You need to set the server-key from Config. Please double-check Config and ServerKey key. '
                . 'You can check from the Midtrans Dashboard. '
                . 'See https://docs.midtrans.com/en/midtrans-account/overview?id=retrieving-api-access-keys '
                . 'for the details or contact support at support@midtrans.com if you have any questions.'
            );
        } else {
            if ($server_key == '') {
                throw new Exception(
                    'The ServerKey/ClientKey is invalid, as it is an empty string. Please double-check your ServerKey key. '
                    . 'You can check from the Midtrans Dashboard. '
                    . 'See https://docs.midtrans.com/en/midtrans-account/overview?id=retrieving-api-access-keys '
                    . 'for the details or contact support at support@midtrans.com if you have any questions.'
                );
            } elseif (preg_match('/\s/', $server_key)) {
                throw new Exception(
                    'The ServerKey/ClientKey is contains white-space. Please double-check your API key. Please double-check your ServerKey key. '
                    . 'You can check from the Midtrans Dashboard. '
                    . 'See https://docs.midtrans.com/en/midtrans-account/overview?id=retrieving-api-access-keys '
                    . 'for the details or contact support at support@midtrans.com if you have any questions.'
                );
            }
        }

        $curl_options = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: midtrans-php-v2.5.2',
                'Authorization: Basic ' . base64_encode($server_key . ':')
            ],
            CURLOPT_RETURNTRANSFER => 1
        ];

        // Set append notification to header
        if (CVendor_Midtrans_Config::$appendNotifUrl) {
            CVendor_Midtrans_Config::$curlOptions[CURLOPT_HTTPHEADER][] = 'X-Append-Notification: ' . CVendor_Midtrans_Config::$appendNotifUrl;
        }
        // Set override notification to header
        if (CVendor_Midtrans_Config::$overrideNotifUrl) {
            CVendor_Midtrans_Config::$curlOptions[CURLOPT_HTTPHEADER][] = 'X-Override-Notification: ' . CVendor_Midtrans_Config::$overrideNotifUrl;
        }
        // Set payment idempotency-key to header
        if (CVendor_Midtrans_Config::$paymentIdempotencyKey) {
            CVendor_Midtrans_Config::$curlOptions[CURLOPT_HTTPHEADER][] = 'Idempotency-Key: ' . CVendor_Midtrans_Config::$paymentIdempotencyKey;
        }

        // merging with CVendor_Midtrans_Config::$curlOptions
        if (count(CVendor_Midtrans_Config::$curlOptions)) {
            // We need to combine headers manually, because it's array and it will no be merged
            if (CVendor_Midtrans_Config::$curlOptions[CURLOPT_HTTPHEADER]) {
                $mergedHeaders = array_merge($curl_options[CURLOPT_HTTPHEADER], CVendor_Midtrans_Config::$curlOptions[CURLOPT_HTTPHEADER]);
                $headerOptions = [CURLOPT_HTTPHEADER => $mergedHeaders];
            } else {
                $mergedHeaders = [];
                $headerOptions = [CURLOPT_HTTPHEADER => $mergedHeaders];
            }

            $curl_options = array_replace_recursive($curl_options, CVendor_Midtrans_Config::$curlOptions, $headerOptions);
        }

        if ($method != 'GET') {
            if ($data_hash) {
                $body = json_encode($data_hash);
                $curl_options[CURLOPT_POSTFIELDS] = $body;
            } else {
                $curl_options[CURLOPT_POSTFIELDS] = '';
            }

            if ($method == 'POST') {
                $curl_options[CURLOPT_POST] = 1;
            } elseif ($method == 'PATCH') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            }
        }

        curl_setopt_array($ch, $curl_options);

        $result = curl_exec($ch);

        if ($result === false) {
            throw new Exception('CURL Error: ' . curl_error($ch), curl_errno($ch));
        } else {
            try {
                $result_array = json_decode($result);
            } catch (Exception $e) {
                throw new Exception('API Request Error unable to json_decode API response: ' . $result . ' | Request url: ' . $url);
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (isset($result_array->status_code) && $result_array->status_code >= 401 && $result_array->status_code != 407) {
                throw new Exception('Midtrans API is returning API error. HTTP status code: ' . $result_array->status_code . ' API response: ' . $result, $result_array->status_code);
            } elseif ($httpCode >= 400) {
                throw new Exception('Midtrans API is returning API error. HTTP status code: ' . $httpCode . ' API response: ' . $result, $httpCode);
            } else {
                return $result_array;
            }
        }
    }
}
