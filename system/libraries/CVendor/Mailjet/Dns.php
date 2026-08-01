<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Keadaan autentikasi domain di Mailjet: SPF, DKIM, dan bukti kepemilikan.
 *
 * Ketiganya sering tertukar. SPF menyatakan siapa yang boleh mengirim atas nama
 * domain, DKIM menandatangani suratnya, dan token kepemilikan membuktikan
 * domainnya memang milik pemegang akun — yang terakhir inilah yang membuat
 *
 * Mailjet mengizinkan `*@domain` sebagai pengirim.
 */
class CVendor_Mailjet_Dns {
    /**
     * @var CVendor_Mailjet
     */
    protected $mailjet;

    public function __construct(CVendor_Mailjet $mailjet) {
        $this->mailjet = $mailjet;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getAll($limit = 200) {
        return $this->mailjet->request('GET', 'dns', [], ['Limit' => (int) $limit]);
    }

    /**
     * Keterangan satu domain, termasuk nilai record yang harus dipasang.
     *
     * @param string $domain
     *
     * @throws CVendor_Mailjet_Exception 404 bila domainnya belum terdaftar
     *
     * @return array
     */
    public function find($domain) {
        $result = $this->mailjet->request('GET', 'dns/' . rawurlencode((string) $domain));

        return carr::get($result, 0, []);
    }

    /**
     * Meminta Mailjet membaca ulang DNS domain ini.
     *
     * Perubahan DNS perlu waktu menyebar, jadi jawaban Error tepat sesudah
     * recordnya dipasang belum tentu berarti salah pasang.
     *
     * @param string $domain
     *
     * @return array
     */
    public function check($domain) {
        $result = $this->mailjet->request('POST', 'dns/' . rawurlencode((string) $domain) . '/check');

        return carr::get($result, 0, []);
    }
}
