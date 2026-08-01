<?php

use DigitalOceanV2\Client;
use DigitalOceanV2\ResultPager;
use DigitalOceanV2\Api\AbstractApi;

/**
 * Pembungkus SDK DigitalOcean (toin0u/digitalocean-v2).
 *
 * Sejak SDK dinaikkan ke 4.9.1 pada 2026-08-02, klien dibangun dengan
 * `new Client()` lalu `authenticate()`, bukan lagi lewat adapter Guzzle. Klien
 * itu mencari implementasi PSR-18 sendiri lewat php-http/discovery, dan Guzzle 7
 * yang ada di `system/vendor` memenuhinya.
 */
class CVendor_DigitalOcean {
    /**
     * Sebanyak-banyaknya yang diizinkan DigitalOcean dalam satu halaman.
     */
    const PER_PAGE = 200;

    /**
     * @var \DigitalOceanV2\Client
     */
    protected $do;

    /**
     * @param string $accessToken
     */
    public function __construct($accessToken) {
        $this->do = new Client();
        $this->do->authenticate((string) $accessToken);
    }

    /**
     * @return \DigitalOceanV2\Client
     */
    public function getObject() {
        return $this->do;
    }

    /**
     * Mengambil **seluruh** isi sebuah koleksi, melintasi berapa pun halamannya.
     *
     * Wajib dipakai untuk setiap `getAll()`. Sejak SDK 4.x, method `getAll()`
     * tidak lagi mengirim `per_page` sama sekali, sehingga DigitalOcean memakai
     * bawaannya — **20 item** — dan mengembalikannya begitu saja tanpa
     * memberitahu bahwa masih ada sisa. Versi 3.x dulu menuliskan `per_page=200`
     * di setiap pemanggilan, jadi memanggil `getAll()` langsung pada 4.x adalah
     * pemotongan diam-diam, bukan sekadar perbedaan gaya.
     *
     * Yang dipotong itu nyata: satu akun DigitalOcean di sini punya 83 domain,
     * dan `ittron.co.id` sendiri berisi 134 record DNS.
     *
     * @param AbstractApi $api        misalnya $do->getObject()->domain()
     * @param string      $method     nama methodnya, biasanya 'getAll'
     * @param array       $parameters argumen untuk method itu
     *
     * @return array
     */
    public function fetchAll(AbstractApi $api, $method, array $parameters = []) {
        $pager = new ResultPager($this->do, self::PER_PAGE);

        return $pager->fetchAll($api, $method, $parameters);
    }
}
