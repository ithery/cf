<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Alamat pengirim yang diizinkan pada sebuah akun Mailjet.
 *
 * Mailjet menolak surat yang alamat pengirimnya tidak terdaftar di sini,
 * **sesudah** autentikasi SMTP berhasil — sebagai 550, bukan 535. Itu sebabnya
 * kunci yang benar saja belum cukup untuk mengirim.
 *
 * Satu entri dapat berupa alamat tunggal (`admin@contoh.com`) atau seluruh
 *
 * domain (`*@contoh.com`); yang kedua baru aktif setelah kepemilikan domainnya
 * dibuktikan lewat record DNS — lihat CVendor_Mailjet_Dns.
 */
class CVendor_Mailjet_Sender {
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
        return $this->mailjet->request('GET', 'sender', [], ['Limit' => (int) $limit]);
    }

    /**
     * @param string $email alamat, atau *@domain untuk seluruh domain
     * @param string $name
     *
     * @return array
     */
    public function create($email, $name = null) {
        $payload = ['Email' => (string) $email];
        if (strlen((string) $name) > 0) {
            $payload['Name'] = (string) $name;
        }

        return $this->mailjet->request('POST', 'sender', $payload);
    }

    /**
     * Meminta Mailjet memeriksa ulang kepemilikan sebuah sender.
     *
     * @param int $senderId
     *
     * @return array
     */
    public function validate($senderId) {
        return $this->mailjet->request('POST', 'sender/' . (int) $senderId . '/validate');
    }

    /**
     * @param int $senderId
     *
     * @return array
     */
    public function delete($senderId) {
        return $this->mailjet->request('DELETE', 'sender/' . (int) $senderId);
    }
}
