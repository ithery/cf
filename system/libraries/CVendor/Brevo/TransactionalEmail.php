<?php

/**
 * Pengiriman email transaksional lewat REST API Brevo.
 *
 * **Sengaja tidak memakai SDK resmi** yang ada di `system/vendor/Brevo`. SDK
 * itu menuntut PHP 8.1 - enum dan `new` di nilai bawaan parameter - sedangkan
 * kerangka kerja ini menyatakan PHP >= 7.4, dan beberapa vhost yang berbagi
 * `system/` yang sama memang masih berjalan di 7.4. Kelas notifikasi dapat
 * dimuat aplikasi mana pun, jadi ia tidak boleh membawa syarat versi yang
 * lebih tinggi daripada kerangka kerjanya sendiri.
 *
 * Yang dipakai di sini hanya satu endpoint, jadi ongkos tidak memakai SDK-nya
 * kecil. Aplikasi yang berjalan di PHP 8.1+ dan membutuhkan permukaan API
 * Brevo selengkapnya tetap boleh memakai SDK itu langsung.
 */
class CVendor_Brevo_TransactionalEmail {
    /**
     * @var string
     */
    const SEND_URL = 'https://api.brevo.com/v3/smtp/email';

    /**
     * @var int
     */
    const TIMEOUT_SECOND = 20;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @param string $apiKey
     */
    public function __construct($apiKey) {
        $this->apiKey = (string) $apiKey;
    }

    /**
     * @return bool
     */
    public function isConfigured() {
        return strlen($this->apiKey) > 0;
    }

    /**
     * Mengirim satu email.
     *
     * @param array $payload badan permintaan Brevo apa adanya - sender, to,
     *                       subject, htmlContent, dan seterusnya
     *
     * @throws CVendor_Brevo_Exception
     *
     * @return array jawaban Brevo, berisi messageId
     */
    public function send(array $payload) {
        if (!$this->isConfigured()) {
            throw new CVendor_Brevo_Exception('Brevo api key belum diisi');
        }

        try {
            $response = CHTTP::client()
                ->timeout(static::TIMEOUT_SECOND)
                ->withHeaders(['api-key' => $this->apiKey, 'accept' => 'application/json'])
                ->post(static::SEND_URL, $payload);
        } catch (Exception $ex) {
            throw new CVendor_Brevo_Exception('Brevo tidak terjangkau: ' . $ex->getMessage(), 0, $ex);
        }

        $body = (array) $response->json();

        if (!$response->successful()) {
            //Brevo menyebutkan alasannya di `message`; tanpa itu pemanggil
            //hanya melihat kode status dan tidak tahu harus memperbaiki apa.
            throw new CVendor_Brevo_Exception(
                'Brevo menolak (' . $response->status() . '): ' . carr::get($body, 'message', $response->body())
            );
        }

        return $body;
    }
}
