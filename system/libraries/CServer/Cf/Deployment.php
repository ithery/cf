<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Pemeriksaan sebelum deploy sebuah aplikasi CF di server.
 *
 * Menjawab satu pertanyaan: kalau di-pull sekarang, apa yang berubah dan adakah
 * yang hilang. Seluruh perintahnya hanya membaca — `fetch` memperbarui referensi
 * remote tetapi tidak menyentuh working tree — sehingga aman dipanggil kapan pun.
 *
 * Berkas yang berubah dikelompokkan menurut tata letak CF, karena akibat sebuah
 * pull berbeda-beda menurut kelompoknya: `system/` mengubah framework untuk
 * seluruh aplikasi di server itu, `application/` hanya satu aplikasi, dan
 * perubahan pada `*.sql` atau `data/patch/` menyiratkan ada langkah lain yang
 * harus dijalankan sesudah pull.
 */
class CServer_Cf_Deployment {
    /**
     * Penanda antar bagian keluaran, dipilih supaya tidak mungkin muncul dalam
     * nama berkas maupun pesan commit.
     */
    const SECTION = '===CFDEPLOY:';

    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var null|string
     */
    protected $user;

    /**
     * @param CServer_Server $server
     * @param string         $path
     * @param null|string    $user pengguna sistem pemilik repositori
     */
    public function __construct(CServer_Server $server, $path, $user = null) {
        $this->server = $server;
        $this->path = rtrim((string) $path, '/');
        $this->user = $user;
    }

    /**
     * Bungkus perintah agar berjalan sebagai pemilik repositori.
     *
     * Menjalankannya sebagai root akan meninggalkan berkas milik root di dalam
     * repositori, yang membuat pull berikutnya oleh pengguna aslinya gagal.
     *
     * @param string $command
     *
     * @return string
     */
    protected function wrap($command) {
        $inner = 'cd ' . escapeshellarg($this->path) . ' && ' . $command;
        if (strlen((string) $this->user) == 0) {
            return $inner;
        }

        return 'sudo runuser -l ' . escapeshellarg($this->user) . ' -c ' . escapeshellarg($inner);
    }

    /**
     * @param array $commandList label => perintah
     *
     * @return array label => keluaran
     */
    protected function runSections(array $commandList) {
        $script = '';
        foreach ($commandList as $label => $command) {
            //`echo` penutup wajib: `git log --pretty=format:` tidak mengakhiri
            //keluarannya dengan baris baru, sehingga penanda bagian berikutnya
            //menempel pada subjek commit terakhir dan seluruh pembagiannya bergeser
            $script .= 'echo "' . self::SECTION . $label . '"; ' . $command . ' 2>/dev/null; echo ""; ';
        }

        $output = (string) $this->server->runCommand($this->wrap($script));

        $result = [];
        $current = null;
        foreach (explode("\n", $output) as $line) {
            $trimmed = rtrim($line, "\r");
            if (substr($trimmed, 0, strlen(self::SECTION)) === self::SECTION) {
                $current = substr($trimmed, strlen(self::SECTION));
                $result[$current] = [];

                continue;
            }
            if ($current !== null) {
                $result[$current][] = $trimmed;
            }
        }

        return $result;
    }

    /**
     * Kelompokkan berkas menurut tata letak CF, beserta apakah kelompoknya
     * menuntut langkah lain sesudah pull.
     *
     * @param array $fileList
     *
     * @return array
     */
    public static function groupChangedFile(array $fileList) {
        $group = [
            'framework' => ['label' => 'Framework (system/)', 'file' => [], 'note' => 'Berlaku untuk semua aplikasi di server ini'],
            'application' => ['label' => 'Aplikasi (application/)', 'file' => [], 'note' => ''],
            'media' => ['label' => 'Media & aset', 'file' => [], 'note' => 'Mungkin perlu build ulang'],
            'database' => ['label' => 'Database', 'file' => [], 'note' => 'Ada perubahan skema atau patch, perlu dijalankan terpisah'],
            'composer' => ['label' => 'Dependensi', 'file' => [], 'note' => 'Perlu composer install'],
            'other' => ['label' => 'Lainnya', 'file' => [], 'note' => ''],
        ];

        foreach ($fileList as $file) {
            $key = 'other';
            if (preg_match('#(^|/)system/#', $file)) {
                $key = 'framework';
            } elseif (preg_match('#\.sql$#i', $file) || preg_match('#(^|/)data/(patch|setup|migration)/#', $file)) {
                $key = 'database';
            } elseif (preg_match('#(^|/)composer\.(json|lock)$#', $file)) {
                $key = 'composer';
            } elseif (preg_match('#(^|/)media/#', $file) || preg_match('#\.(js|css|scss|vue)$#i', $file)) {
                $key = 'media';
            } elseif (preg_match('#(^|/)application/#', $file)) {
                $key = 'application';
            }
            $group[$key]['file'][] = $file;
        }

        foreach (array_keys($group) as $key) {
            if (count($group[$key]['file']) == 0) {
                unset($group[$key]);
            }
        }

        return $group;
    }

    /**
     * Keadaan repositori dan akibat sebuah pull, tanpa menjalankannya.
     *
     * @return array
     */
    public function analyzePull() {
        $section = $this->runSections([
            'exists' => '[ -d .git ] && echo yes || echo no',
            'branch' => 'git rev-parse --abbrev-ref HEAD',
            'upstream' => 'git rev-parse --abbrev-ref --symbolic-full-name @{u}',
            'fetch' => 'git fetch --quiet && echo ok || echo fail',
            'counts' => 'git rev-list --left-right --count HEAD...@{u}',
            'dirty' => 'git status --porcelain',
            'incoming' => 'git log --pretty=format:%h%x09%an%x09%ad%x09%s --date=short HEAD..@{u}',
            'changed' => 'git diff --numstat HEAD..@{u}',
        ]);

        $first = function ($label) use ($section) {
            $line = carr::get($section, $label, []);
            foreach ($line as $value) {
                if (strlen(trim((string) $value)) > 0) {
                    return trim((string) $value);
                }
            }

            return '';
        };

        $result = [
            'path' => $this->path,
            'exists' => $first('exists') == 'yes',
            'branch' => $first('branch'),
            'upstream' => $first('upstream'),
            'fetched' => $first('fetch') == 'ok',
            'ahead' => 0,
            'behind' => 0,
            'dirty' => [],
            'untracked' => [],
            'incoming' => [],
            'changed' => [],
            'conflict' => [],
            'safe' => false,
            'reason' => '',
        ];

        if (!$result['exists']) {
            $result['reason'] = 'Direktori ini bukan repositori git.';

            return $result;
        }
        if (strlen($result['upstream']) == 0) {
            $result['reason'] = 'Branch ini tidak punya upstream, jadi tidak ada yang dapat ditarik.';

            return $result;
        }

        $count = preg_split('/\s+/', $first('counts'));
        $result['ahead'] = (int) carr::get($count, 0, 0);
        $result['behind'] = (int) carr::get($count, 1, 0);

        foreach (carr::get($section, 'dirty', []) as $line) {
            if (strlen(trim((string) $line)) == 0) {
                continue;
            }
            $status = trim(substr($line, 0, 2));
            $file = trim(substr($line, 3));
            if ($status == '??') {
                $result['untracked'][] = $file;

                continue;
            }
            $result['dirty'][] = ['status' => $status, 'file' => $file];
        }

        foreach (carr::get($section, 'incoming', []) as $line) {
            if (strlen(trim((string) $line)) == 0) {
                continue;
            }
            $part = explode("\t", $line, 4);
            $result['incoming'][] = [
                'hash' => (string) carr::get($part, 0),
                'author' => (string) carr::get($part, 1),
                'date' => (string) carr::get($part, 2),
                'subject' => (string) carr::get($part, 3),
            ];
        }

        $changedFile = [];
        foreach (carr::get($section, 'changed', []) as $line) {
            if (strlen(trim((string) $line)) == 0) {
                continue;
            }
            $part = preg_split('/\t/', $line, 3);
            $file = (string) carr::get($part, 2);
            $changedFile[] = $file;
            $result['changed'][] = [
                'added' => (string) carr::get($part, 0),
                'deleted' => (string) carr::get($part, 1),
                'file' => $file,
            ];
        }

        //berkas yang diubah di server sekaligus diubah di remote: pull akan
        //ditolak git, atau perubahan lokalnya hilang bila dipaksa
        foreach ($result['dirty'] as $item) {
            if (in_array(carr::get($item, 'file'), $changedFile)) {
                $result['conflict'][] = carr::get($item, 'file');
            }
        }

        $result['group'] = static::groupChangedFile($changedFile);
        $result['safe'] = count($result['conflict']) == 0 && count($result['dirty']) == 0;
        if (!$result['fetched']) {
            $result['reason'] = 'Gagal menghubungi remote, jadi daftar perubahannya belum tentu mutakhir.';
        } elseif (count($result['conflict']) > 0) {
            $result['reason'] = 'Ada berkas yang diubah di server sekaligus berubah di remote. Pull akan ditolak.';
        } elseif (count($result['dirty']) > 0) {
            $result['reason'] = 'Ada perubahan lokal yang belum dicommit. Pull tetap berjalan, tetapi perubahan itu bercampur dengan yang masuk.';
        } elseif ($result['behind'] == 0) {
            $result['reason'] = 'Sudah mutakhir, tidak ada yang perlu ditarik.';
        } else {
            $result['reason'] = 'Tidak ada perubahan lokal, pull akan berjalan bersih.';
        }

        return $result;
    }
}
