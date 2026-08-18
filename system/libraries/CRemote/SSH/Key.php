<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Pembuatan dan pembacaan pasangan kunci SSH.
 *
 * Memakai `ssh-keygen` alih-alih membangun sendiri dari openssl, karena format
 * OpenSSH punya pembungkus tersendiri untuk kunci privat dan baris publiknya —
 * menyusunnya sendiri mudah menghasilkan kunci yang tampak benar tetapi ditolak
 * saat dipakai. Sidik jari pun dihitung oleh ssh-keygen sehingga sama persis
 * dengan yang ditampilkan server.
 */
class CRemote_SSH_Key {
    const TYPE_ED25519 = 'ed25519';

    const TYPE_RSA = 'rsa';

    const TYPE_ECDSA = 'ecdsa';

    /**
     * Jenis kunci yang didukung beserta ukuran bit bawaannya. ed25519
     * didahulukan: panjangnya tetap, lebih cepat, dan dianjurkan OpenSSH.
     *
     * @return array
     */
    public static function typeList() {
        return [
            self::TYPE_ED25519 => 'ED25519 (dianjurkan)',
            self::TYPE_RSA => 'RSA',
            self::TYPE_ECDSA => 'ECDSA',
        ];
    }

    /**
     * @param string $type
     *
     * @return array ukuran bit yang sah untuk jenis ini
     */
    public static function bitsList($type) {
        $bits = [
            self::TYPE_RSA => [2048, 3072, 4096],
            self::TYPE_ECDSA => [256, 384, 521],
            self::TYPE_ED25519 => [],
        ];

        return carr::get($bits, $type, []);
    }

    /**
     * @return bool
     */
    public static function isAvailable() {
        $output = [];
        $status = 0;
        @exec('command -v ssh-keygen 2>/dev/null', $output, $status);

        return $status === 0 && count($output) > 0;
    }

    /**
     * Membuat pasangan kunci baru.
     *
     * Kunci ditulis ke direktori sementara lalu dibaca dan dihapus, karena
     * ssh-keygen hanya menulis ke berkas. Penghapusan dijalankan lewat finally
     * sehingga tetap terjadi walau pembacaan gagal.
     *
     * @param string      $type
     * @param null|int    $bits       diabaikan untuk ed25519
     * @param string      $comment
     * @param null|string $passphrase
     *
     * @throws CException
     *
     * @return array private, public, fingerprint, type, bits, comment
     */
    public static function generate($type = self::TYPE_ED25519, $bits = null, $comment = '', $passphrase = null) {
        if (!array_key_exists($type, self::typeList())) {
            throw new CServer_Exception_UnsupportedKeyTypeException($type, self::typeList());
        }
        if (!self::isAvailable()) {
            throw new CServer_Exception_CommandNotFoundException(
                'ssh-keygen',
                CServer_Exception_CommandNotFoundException::LOCATION_LOCAL
            );
        }

        $validBits = self::bitsList($type);
        if (count($validBits) > 0) {
            if ($bits === null) {
                $bits = $validBits[0];
            }
            if (!in_array((int) $bits, $validBits)) {
                throw new CServer_Exception_InvalidKeyBitsException($bits, $type, $validBits);
            }
        } else {
            $bits = null;
        }

        $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            . 'cf-sshkey-' . bin2hex(random_bytes(8));
        if (!@mkdir($dir, 0700, true)) {
            throw new CServer_Exception_KeyGenerationFailedException(
                'tidak dapat membuat direktori sementara ' . $dir
            );
        }
        $path = $dir . DIRECTORY_SEPARATOR . 'id_key';

        try {
            $command = 'ssh-keygen -t ' . escapeshellarg($type);
            if ($bits !== null) {
                $command .= ' -b ' . escapeshellarg((string) $bits);
            }
            $command .= ' -C ' . escapeshellarg((string) $comment);
            //-N kosong berarti tanpa frasa sandi; -q menekan keluaran interaktif
            $command .= ' -N ' . escapeshellarg((string) $passphrase);
            $command .= ' -f ' . escapeshellarg($path) . ' -q 2>&1';

            $output = [];
            $status = 0;
            @exec($command, $output, $status);

            if ($status !== 0 || !is_file($path) || !is_file($path . '.pub')) {
                throw new CServer_Exception_KeyGenerationFailedException(
                    'ssh-keygen tidak menghasilkan berkas kunci',
                    implode("\n", $output)
                );
            }

            $private = (string) @file_get_contents($path);
            $public = trim((string) @file_get_contents($path . '.pub'));

            return [
                'private' => $private,
                'public' => $public,
                'fingerprint' => self::fingerprintFromFile($path . '.pub'),
                'type' => $type,
                'bits' => $bits !== null ? (int) $bits : self::bitsFromPublicKey($public),
                'comment' => (string) $comment,
            ];
        } finally {
            //kunci privat tidak boleh tertinggal di direktori sementara
            foreach ([$path, $path . '.pub'] as $f) {
                if (is_file($f)) {
                    @unlink($f);
                }
            }
            @rmdir($dir);
        }
    }

    /**
     * Membaca keterangan dari sebuah baris kunci publik.
     *
     * Dipakai untuk kunci yang dimasukkan manual, sehingga jenis, ukuran, dan
     * sidik jarinya tetap tercatat tanpa perlu diketik ulang oleh pengguna.
     *
     * @param string $publicKey
     *
     * @return array type, bits, comment, fingerprint
     */
    public static function inspectPublicKey($publicKey) {
        $publicKey = trim((string) $publicKey);
        $result = ['type' => null, 'bits' => null, 'comment' => null, 'fingerprint' => null];
        if (strlen($publicKey) == 0) {
            return $result;
        }

        $part = preg_split('/\s+/', $publicKey);
        $algo = (string) carr::get($part, 0);
        if (strpos($algo, 'ssh-ed25519') === 0) {
            $result['type'] = self::TYPE_ED25519;
        } elseif (strpos($algo, 'ssh-rsa') === 0) {
            $result['type'] = self::TYPE_RSA;
        } elseif (strpos($algo, 'ecdsa-') === 0) {
            $result['type'] = self::TYPE_ECDSA;
        }
        if (count($part) > 2) {
            $result['comment'] = implode(' ', array_slice($part, 2));
        }

        if (!self::isAvailable()) {
            return $result;
        }

        $file = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            . 'cf-sshpub-' . bin2hex(random_bytes(8)) . '.pub';

        try {
            @file_put_contents($file, $publicKey . "\n");
            @chmod($file, 0600);
            $result['fingerprint'] = self::fingerprintFromFile($file);
            $result['bits'] = self::bitsFromFile($file);
        } finally {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        return $result;
    }

    /**
     * @param string $file
     *
     * @return null|string
     */
    protected static function fingerprintFromFile($file) {
        $output = [];
        @exec('ssh-keygen -l -E sha256 -f ' . escapeshellarg($file) . ' 2>/dev/null', $output);
        $line = trim((string) carr::get($output, 0));
        if (strlen($line) == 0) {
            return null;
        }
        //bentuknya "256 SHA256:xxxx comment (ED25519)"
        if (preg_match('/(SHA256:[A-Za-z0-9+\/=]+)/', $line, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * @param string $file
     *
     * @return null|int
     */
    protected static function bitsFromFile($file) {
        $output = [];
        @exec('ssh-keygen -l -f ' . escapeshellarg($file) . ' 2>/dev/null', $output);
        $line = trim((string) carr::get($output, 0));
        if (preg_match('/^(\d+)\s/', $line, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /**
     * @param string $publicKey
     *
     * @return null|int
     */
    protected static function bitsFromPublicKey($publicKey) {
        $info = self::inspectPublicKey($publicKey);

        return carr::get($info, 'bits');
    }
}
