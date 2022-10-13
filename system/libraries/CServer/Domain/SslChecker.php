<?php

class CServer_Domain_SslChecker {
    protected $urls;

    protected $result;

    protected $dateFormat;

    protected $formatString;

    protected $timeZone;

    protected $timeOut;

    /**
     * CheckSSL constructor.
     *
     * @param array       $url
     * @param string      $dateFormat
     * @param string      $formatString
     * @param null|string $timeZone
     * @param float       $timeOut
     *
     * @throws Exception
     */
    public function __construct(array $url = [], $dateFormat = 'U', $formatString = 'Y-m-d\T    H:i:s\Z', $timeZone = null, $timeOut = 30) {
        !empty($url) ? $this->add($url) : $this->urls = $url;
        $this->dateFormat = $dateFormat;
        $this->timeZone = $timeZone;
        $this->formatString = $formatString;
        $this->timeOut = $timeOut;
        $this->result = [];
    }

    /**
     * @param array $data
     *
     * @throws \Exception
     *
     * @return DDomain_CheckSSL
     */
    public function add(...$data) {
        /** @var array|string $url */
        foreach ($data as $url) {
            if (c::isIterable($url)) {
                foreach ($url as $i) {
                    $this->add($i);
                }

                continue;
            }

            if (empty($url)) {
                throw new \Exception('please  target url is empty');
            }

            if (!$this->isValidUrl($url)) {
                throw new \Exception('malformed URLs');
            }

            $cleanUrl = parse_url($url, PHP_URL_HOST);

            if ($cleanUrl === null) {
                throw new \Exception('seriously malformed URLs');
            }

            $this->urls[] = $cleanUrl;
        }

        return $this;
    }

    /**
     * @throws Exception
     *
     * @return array
     */
    public function check() {
        foreach ($this->urls as $item) {
            /** @var resource|false $cert */
            $cert = $this->getCert($item);

            if ($cert === false) {
                $this->result[$item] = null;

                continue;
            }

            $this->result[$item] = $this->getSSLInformation($cert);
        }

        return $this->getResults();
    }

    public function getTimeout() {
        return $this->timeOut;
    }

    /**
     * @param resource|false $siteStream
     *
     * @throws Exception
     *
     * @return array
     */
    private function getSSLInformation($siteStream) {
        try {
            if (!is_resource($siteStream) || get_resource_type($siteStream) !== 'stream') {
                throw new RuntimeException('param $siteStream not type stream');
            }

            $certStream = stream_context_get_params($siteStream);

            $cert = $this->getCertFromArray($certStream);

            $certInfo = openssl_x509_parse($cert);

            $isValid = time() <= $certInfo['validTo_time_t'];
            $validFrom = $this->normalizeDate((string) $certInfo['validFrom_time_t']);
            $validTo = $this->normalizeDate((string) $certInfo['validTo_time_t']);
            $commonName = carr::get($certInfo, 'subject.CN');
            $issuer = carr::get($certInfo, 'issuer.CN');
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }

        return [
            'isValid' => $isValid,
            'createdAt' => $validFrom,
            'validUntil' => $validTo,
            'commonName' => $commonName,
            'issuer' => $issuer,
            'raw' => $certInfo,
        ];
    }

    /**
     * @return array|mixed
     */
    private function getResults() {
        if (count($this->result) === 1) {
            return current($this->result);
        }

        return $this->result;
    }

    /**
     * @return resource
     */
    private function getStreamContext() {
        return stream_context_create(
            [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'capture_peer_cert' => true
                ]
            ]
        );
    }

    /**
     * @param string $url
     *
     * @return false|resource
     */
    private function getCert(string $url) {
        try {
            $messageError = 'error to get certificate';
            $cert = @stream_socket_client(
                'ssl://' . $url . ':443',
                $errno,
                $messageError,
                $this->timeOut,
                STREAM_CLIENT_CONNECT,
                $this->getStreamContext()
            );
        } catch (\Exception $exception) {
            throw new CServer_Domain_Exception_SslNotSupportedException($exception->getMessage());
        }

        return  $cert;
    }

    /**
     * @param string $timeStamp
     *
     * @return string|false
     */
    private function normalizeDate($timeStamp) {
        $timeZone = null;

        if ($this->timeZone !== null) {
            $timeZone = new DateTimeZone($this->timeZone);
        }

        return DateTime::createFromFormat($this->dateFormat, $timeStamp, $timeZone)->format($this->formatString);
    }

    /**
     * @param array $certStream
     *
     * @return mixed
     */
    private function getCertFromArray(array $certStream) {
        return $certStream['options']['ssl']['peer_certificate'];
    }

    /**
     * @param string $data
     *
     * @return bool
     */
    private function isValidUrl($data) {
        $regex
            = "%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*"
            . "[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*"
            . "(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu";

        return 1 === preg_match($regex, $data);
    }
}
