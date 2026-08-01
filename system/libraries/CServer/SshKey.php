<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Pembuatan pasangan kunci SSH pada sebuah server.
 *
 * Berbeda dari CRemote_SSH_Key yang selalu bekerja di mesin tempat PHP
 * berjalan, kelas ini menjalankan ssh-keygen di server tujuan — berguna ketika
 * kunci memang harus lahir di sana, misalnya deploy key yang tidak boleh
 * melewati jaringan dalam bentuk privat.
 *
 * Daftar jenis dan ukuran bit sengaja diambil dari CRemote_SSH_Key agar hanya
 * ada satu sumber kebenaran.
 */
class CServer_SshKey {
    /**
     * @var CServer_Server
     */
    protected $server;

    public function __construct(CServer_Server $server) {
        $this->server = $server;
    }

    /**
     * @return CServer_Server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @param array|string $command
     *
     * @return string
     */
    protected function run($command) {
        return $this->server->runCommand($command);
    }

    /**
     * @return bool
     */
    public function isAvailable() {
        $output = trim($this->run('command -v ssh-keygen >/dev/null 2>&1 && echo yes || echo no'));

        return strpos($output, 'yes') !== false;
    }

    /**
     * Membuat pasangan kunci di server ini.
     *
     * Kunci ditulis ke direktori sementara, dibaca, lalu dihapus dalam satu
     * rangkaian perintah — supaya kunci privat tidak tertinggal di server walau
     * pembacaannya gagal di tengah jalan.
     *
     * @param string      $type
     * @param null|int    $bits
     * @param string      $comment
     * @param null|string $passphrase
     *
     * @throws CException
     *
     * @return array private, public, fingerprint, type, bits, comment
     */
    public function generate($type = CRemote_SSH_Key::TYPE_ED25519, $bits = null, $comment = '', $passphrase = null) {
        if (!array_key_exists($type, CRemote_SSH_Key::typeList())) {
            throw new CServer_Exception_UnsupportedKeyTypeException($type, CRemote_SSH_Key::typeList());
        }

        $validBits = CRemote_SSH_Key::bitsList($type);
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

        if (!$this->isAvailable()) {
            throw new CServer_Exception_CommandNotFoundException(
                'ssh-keygen',
                CServer_Exception_CommandNotFoundException::LOCATION_REMOTE
            );
        }

        //penanda dipakai agar tiga bagian keluaran dapat dipisahkan dengan pasti,
        //karena isi kunci sendiri mengandung banyak baris
        $begin = '===CF-KEY-BEGIN===';
        $mid = '===CF-KEY-MID===';
        $end = '===CF-KEY-END===';

        $script = 'D=$(mktemp -d) && cd "$D" && '
            . 'ssh-keygen -t ' . escapeshellarg($type)
            . ($bits !== null ? ' -b ' . escapeshellarg((string) $bits) : '')
            . ' -C ' . escapeshellarg((string) $comment)
            . ' -N ' . escapeshellarg((string) $passphrase)
            . ' -f "$D/id_key" -q >/dev/null 2>&1 && '
            . 'echo "' . $begin . '" && cat "$D/id_key" && '
            . 'echo "' . $mid . '" && cat "$D/id_key.pub" && '
            . 'echo "' . $mid . '" && ssh-keygen -l -E sha256 -f "$D/id_key.pub" && '
            . 'echo "' . $end . '"; '
            //dijalankan apa pun hasilnya, supaya kunci privat tidak tertinggal
            . 'rm -f "$D/id_key" "$D/id_key.pub"; rmdir "$D" 2>/dev/null';

        $output = (string) $this->run($script);

        if (strpos($output, $begin) === false || strpos($output, $end) === false) {
            throw new CServer_Exception_KeyGenerationFailedException(
                'ssh-keygen di server tujuan tidak menghasilkan keluaran yang diharapkan',
                $output
            );
        }

        $body = cstr::between($output, $begin, $end);
        $part = explode($mid, $body);

        $private = trim((string) carr::get($part, 0));
        $public = trim((string) carr::get($part, 1));
        $info = trim((string) carr::get($part, 2));

        if (strlen($private) == 0 || strlen($public) == 0) {
            throw new CServer_Exception_KeyGenerationFailedException('berkas kunci yang dihasilkan kosong', $output);
        }

        $fingerprint = null;
        if (preg_match('/(SHA256:[A-Za-z0-9+\/=]+)/', $info, $m)) {
            $fingerprint = $m[1];
        }
        $bitsHasil = $bits;
        if ($bitsHasil === null && preg_match('/^(\d+)\s/', $info, $m)) {
            $bitsHasil = (int) $m[1];
        }

        return [
            'private' => $private . PHP_EOL,
            'public' => $public,
            'fingerprint' => $fingerprint,
            'type' => $type,
            'bits' => $bitsHasil !== null ? (int) $bitsHasil : null,
            'comment' => (string) $comment,
        ];
    }

    /**
     * Menambahkan sebuah kunci publik ke authorized_keys pengguna di server ini.
     *
     * Tidak menambah bila baris yang sama sudah ada, sehingga aman dipanggil
     * berulang.
     *
     * @param string      $publicKey
     * @param null|string $user      bila null, pengguna koneksi saat ini
     *
     * @return string keluaran perintah
     */
    public function addAuthorizedKey($publicKey, $user = null) {
        $publicKey = trim((string) $publicKey);
        if (strlen($publicKey) == 0) {
            throw new CServer_Exception_InvalidPublicKeyException('nilainya kosong', $publicKey);
        }

        $home = $user === null ? '$HOME' : '$(getent passwd ' . escapeshellarg($user) . ' | cut -d: -f6)';

        return $this->run(
            'H=' . $home . ' && mkdir -p "$H/.ssh" && chmod 700 "$H/.ssh" && '
            . 'touch "$H/.ssh/authorized_keys" && chmod 600 "$H/.ssh/authorized_keys" && '
            . 'grep -qxF ' . escapeshellarg($publicKey) . ' "$H/.ssh/authorized_keys" '
            . '&& echo "kunci sudah ada, tidak ditambahkan" '
            . '|| { echo ' . escapeshellarg($publicKey) . ' >> "$H/.ssh/authorized_keys"; echo "kunci ditambahkan"; }'
        );
    }
}
