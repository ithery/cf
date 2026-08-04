<?php

class CVendor_LiteSpeed {
    const DEFAULT_SERVER_ROOT = '/usr/local/lsws';

    public static function serverRoot() {
        return static::DEFAULT_SERVER_ROOT;
    }

    /**
     * Mengurai isi berkas konfigurasi yang sudah berada di memori.
     *
     * `CVendor_LiteSpeed_Data` membaca dari jalur berkas, sedangkan konfigurasi
     * yang menarik hampir selalu datang dari mesin lain lewat SSH — berupa
     * string, bukan berkas. Sebelum ini tiap pemanggil menyiasatinya sendiri
     * dengan menulis berkas sementara, dan tak satu pun menghapusnya kembali.
     *
     * Berkas sementaranya dihapus segera sesudah diurai: `Data::__construct()`
     * membaca isinya di tempat, jadi tidak ada yang masih memerlukannya.
     *
     * @param string $configType lihat `CVendor_LiteSpeed_Info::CT_*`
     * @param string $content    isi berkas konfigurasi
     *
     * @return CVendor_LiteSpeed_Data
     */
    public static function parseConf($configType, $content) {
        $disk = CStorage::instance()->disk('local-temp');
        $file = CTemporary::getPath('litespeedconf', date('Ymd') . cutils::randmd5() . '.conf');
        $disk->put($file, (string) $content);

        try {
            $data = new CVendor_LiteSpeed_Data($configType, $disk->path($file));
        } finally {
            //dihapus lewat `finally` supaya berkas berisi konfigurasi server
            //tidak tertinggal ketika penguraiannya gagal
            $disk->delete($file);
        }

        return $data;
    }

    /**
     * Potongan sumber asli sebuah simpul, berikut baris pembuka dan penutupnya.
     *
     * Pohon hasil urai **mengelompokkan ulang** anak menurut kunci, dan untuk
     * sebagian blok pengelompokan itu mengubah arti. Contoh yang paling nyata
     * ada di `rewrite`: seluruh `RewriteRule` berkumpul lebih dulu, seluruh
     * `RewriteCond` menyusul di belakangnya — padahal sebuah `RewriteCond`
     * hanya berlaku bagi `RewriteRule` yang **tepat sesudahnya**. Menampilkan
     * pohonnya berarti menampilkan aturan yang tidak pernah ada di berkas.
     *
     * Untuk blok semacam itu yang benar adalah menunjukkan teks aslinya. Simpul
     * sudah menyimpan rentang barisnya, jadi tinggal dipotong.
     *
     * @param CVendor_LiteSpeed_Node $node
     * @param string                 $content isi berkas yang menghasilkan simpul ini
     *
     * @return string
     */
    public static function nodeSource(CVendor_LiteSpeed_Node $node, $content) {
        return self::sliceLine($content, $node, 0, 0);
    }

    /**
     * Isi sebuah blok tanpa baris pembuka dan penutupnya.
     *
     * @param CVendor_LiteSpeed_Node $node
     * @param string                 $content
     *
     * @return string
     */
    public static function nodeBodySource(CVendor_LiteSpeed_Node $node, $content) {
        return self::sliceLine($content, $node, 1, -1);
    }

    /**
     * @param string                 $content
     * @param CVendor_LiteSpeed_Node $node
     * @param int                    $offsetFrom digeser dari baris pertama simpul
     * @param int                    $offsetTo   digeser dari baris terakhir simpul
     *
     * @return string
     */
    protected static function sliceLine($content, CVendor_LiteSpeed_Node $node, $offsetFrom, $offsetTo) {
        $from = (int) $node->get(CVendor_LiteSpeed_Node::FLD_FLFROM) + $offsetFrom;
        $to = (int) $node->get(CVendor_LiteSpeed_Node::FLD_FLTO) + $offsetTo;
        if ($from < 1 || $to < $from) {
            return '';
        }

        //nomor barisnya berbasis satu, indeks lariknya berbasis nol
        $line = preg_split('/\r\n|\r|\n/', (string) $content);

        return implode("\n", array_slice($line, $from - 1, $to - $from + 1));
    }

    /**
     * Menyulih variabel jalur LiteSpeed menjadi jalur mutlak.
     *
     * `configFile` sebuah virtual host lazim ditulis `$SERVER_ROOT/conf/...`
     * atau relatif terhadap `conf/`, sehingga jalur sesungguhnya tidak dapat
     * ditebak dari nama vhost-nya. Menebaknya — misalnya selalu
     * `conf/vhosts/<nama>/vhconf.conf` — benar untuk pemasangan bawaan dan
     * salah untuk yang disusun panel.
     *
     * @param string      $path
     * @param null|string $vhRoot
     *
     * @return string
     */
    public static function expandPath($path, $vhRoot = null) {
        $path = trim((string) $path);
        if (strlen($path) == 0) {
            return '';
        }

        $root = self::serverRoot();
        $path = str_replace('$SERVER_ROOT', $root, $path);
        if (strlen((string) $vhRoot) > 0) {
            $path = str_replace('$VH_ROOT', rtrim((string) $vhRoot, '/'), $path);
        }

        if (substr($path, 0, 1) != '/') {
            $path = $root . '/conf/' . ltrim($path, '/');
        }

        return $path;
    }
}
