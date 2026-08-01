<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Klien REST Mailjet v3.
 *
 * Ditulis sendiri, bukan porting SDK resminya: SDK Mailjet membawa Guzzle dan
 * lapisan konfigurasinya sendiri untuk hal yang di sini hanya berupa beberapa
 * permintaan HTTP dengan basic auth. Yang dibutuhkan justru bagian yang jarang
 * disorot SDK-nya — daftar pengirim dan keadaan SPF/DKIM per domain.
 *
 * Autentikasinya sepasang nilai: API Key sebagai nama pengguna dan Secret Key
 * sebagai kata sandi. Keduanya sama dengan kredensial SMTP relainya, sehingga
 * satu akun cukup dipegang sekali.
 */
class CVendor_Mailjet {
    const BASE_URL = 'https://api.mailjet.com/v3/REST/';

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var null|CVendor_Mailjet_Sender
     */
    protected $sender;

    /**
     * @var null|CVendor_Mailjet_Dns
     */
    protected $dns;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param array  $options   timeout, base_url
     */
    public function __construct($apiKey, $apiSecret, array $options = []) {
        $this->apiKey = (string) $apiKey;
        $this->apiSecret = (string) $apiSecret;
        $this->options = array_merge([
            'timeout' => 20,
            'connect_timeout' => 10,
            'base_url' => self::BASE_URL,
        ], $options);
    }

    /**
     * Daftar dan pengelolaan alamat pengirim.
     *
     * @return CVendor_Mailjet_Sender
     */
    public function sender() {
        if ($this->sender == null) {
            $this->sender = new CVendor_Mailjet_Sender($this);
        }

        return $this->sender;
    }

    /**
     * Keadaan SPF dan DKIM per domain.
     *
     * @return CVendor_Mailjet_Dns
     */
    public function dns() {
        if ($this->dns == null) {
            $this->dns = new CVendor_Mailjet_Dns($this);
        }

        return $this->dns;
    }

    /**
     * Satu permintaan ke REST API.
     *
     * Mengembalikan isi `Data` apa adanya: seluruh sumber daya Mailjet v3
     * membungkus hasilnya di sana, dan pemanggil tidak perlu mengurus
     * pembungkusnya.
     *
     * @param string $method
     * @param string $path
     *
     * @throws CVendor_Mailjet_Exception
     *
     * @return array
     */
    public function request($method, $path, array $payload = [], array $query = []) {
        $url = carr::get($this->options, 'base_url') . $path;
        if (count($query) > 0) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($query);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret),
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => carr::get($this->options, 'timeout'),
            CURLOPT_CONNECTTIMEOUT => carr::get($this->options, 'connect_timeout'),
        ]);
        if (count($payload) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (strlen((string) $error) > 0) {
            throw CVendor_Mailjet_Exception::fromStatus('Tidak dapat menghubungi Mailjet: ' . $error, $code);
        }

        $decoded = json_decode((string) $body, true);
        if ($code >= 400) {
            $message = carr::get($decoded, 'ErrorMessage');
            if (strlen((string) $message) == 0) {
                $message = cstr::limit((string) $body, 200);
            }

            throw CVendor_Mailjet_Exception::fromStatus('Mailjet menjawab HTTP ' . $code . ': ' . $message, $code);
        }

        //sebagian sumber daya menjawab tanpa pembungkus Data, misalnya galat
        //validasi yang tetap ber-HTTP 200
        return is_array($decoded) && array_key_exists('Data', $decoded)
            ? (array) $decoded['Data'] : (is_array($decoded) ? $decoded : []);
    }
}
