<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Mengenali pemasangan Cresenity Framework pada sebuah server.
 *
 * Yang dijawab bukan "apakah ada berkas PHP di sana", melainkan tiga hal yang
 * benar-benar ditanyakan saat menelusuri sebuah document root:
 *
 * 1. apakah ini CF sama sekali;
 * 2. aplikasi mana saja yang tinggal di dalamnya;
 * 3. kode yang terpasang itu **versi yang mana** — cabang dan commit-nya.
 *
 * Yang ketiga yang paling sering menjelaskan keanehan. Satu mesin bisa
 * tertinggal berbulan-bulan dari saudaranya tanpa terlihat dari luar, dan
 * gejalanya muncul sebagai perilaku yang berbeda antar-permintaan, bukan
 * sebagai galat.
 *
 * Seluruh pemeriksaan dikemas dalam **satu** perintah SSH. Tiap perjalanan
 * berbiaya mahal, dan halaman yang memanggil ini kerap memeriksa beberapa
 * document root sekaligus.
 */
class CServer_Cf {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @param CServer_Server $server
     */
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
     * Bentuk jawaban ketika jalurnya bukan CF, atau tidak dapat diperiksa.
     *
     * @param string $docRoot
     * @param string $reason
     *
     * @return array
     */
    protected static function notCf($docRoot, $reason) {
        return [
            'isCf' => false,
            'reason' => $reason,
            'docRoot' => (string) $docRoot,
            'realPath' => '',
            'version' => '',
            'branch' => '',
            'commit' => '',
            'commitDate' => '',
            'appList' => [],
        ];
    }

    /**
     * Memeriksa satu document root.
     *
     * Yang diperiksa **hanya jalur itu sendiri**, tidak merambat ke induk
     * maupun anaknya. Sebuah vhost menunjuk ke satu direktori, dan pertanyaan
     * yang dijawab di sini adalah apakah direktori **itu** akar CF — bukan
     * apakah ada CF di suatu tempat di dekatnya. Menebak lebih jauh justru
     * menghasilkan jawaban yang tampak meyakinkan untuk vhost yang sebenarnya
     * tidak melayani CF sama sekali.
     *
     * @param string $docRoot
     *
     * @return array
     */
    public function inspect($docRoot) {
        $docRoot = trim((string) $docRoot);
        if (strlen($docRoot) == 0) {
            return self::notCf($docRoot, 'document root tidak diketahui');
        }
        //jalur datang dari konfigurasi server, bukan dari pengguna — tetap
        //dikutip karena ia masuk ke perintah shell
        $quoted = escapeshellarg($docRoot);

        $script = 'root=' . $quoted . '; '
            . 'if [ ! -f "$root/system/core/CF.php" ]; then echo "CF|no"; exit 0; fi; '
            . 'root=$(cd "$root" 2>/dev/null && pwd); '
            . 'echo "CF|yes"; '
            . 'echo "ROOT|$root"; '
            . 'echo "INDEX|$([ -f "$root/index.php" ] && echo 1 || echo 0)"; '
            . 'v=$(grep -m1 -oE "CF_VERSION[^0-9]*[0-9]+\\.[0-9]+(\\.[0-9]+)?" "$root/index.php" 2>/dev/null'
            . '     | grep -oE "[0-9]+\\.[0-9]+(\\.[0-9]+)?"); '
            . 'echo "VERSION|$v"; '
            . 'echo "FINGERPRINT|$(md5sum "$root/system/core/CF.php" 2>/dev/null | cut -c1-12)"; '
            . 'if [ -d "$root/.git" ]; then '
            . '  echo "BRANCH|$(cd "$root" && git rev-parse --abbrev-ref HEAD 2>/dev/null)"; '
            . '  echo "COMMIT|$(cd "$root" && git rev-parse --short HEAD 2>/dev/null)"; '
            . '  echo "COMMITDATE|$(cd "$root" && git log -1 --format=%cd --date=short 2>/dev/null)"; '
            . 'fi; '
            . 'for d in "$root"/application/*/; do '
            . '  [ -d "$d" ] || continue; '
            . '  n=$(basename "$d"); '
            . '  b=""; c=""; '
            . '  if [ -d "$d/.git" ]; then '
            . '    b=$(cd "$d" && git rev-parse --abbrev-ref HEAD 2>/dev/null); '
            . '    c=$(cd "$d" && git rev-parse --short HEAD 2>/dev/null); '
            . '  fi; '
            . '  echo "APP|$n|$([ -f "$d/bootstrap.php" ] && echo 1 || echo 0)|$b|$c"; '
            . 'done';

        $output = (string) $this->server->runCommand($script);
        if (strpos($output, 'CF|yes') === false) {
            return self::notCf($docRoot, 'tidak ada system/core/CF.php di jalur ini maupun induknya');
        }

        $result = self::notCf($docRoot, '');
        $result['isCf'] = true;
        $result['reason'] = '';
        $appList = [];

        foreach (explode("\n", $output) as $line) {
            $line = trim($line);
            $part = explode('|', $line);
            $key = (string) carr::get($part, 0);
            if ($key == 'ROOT') {
                $result['realPath'] = (string) carr::get($part, 1);
            } elseif ($key == 'VERSION') {
                $result['version'] = (string) carr::get($part, 1);
            } elseif ($key == 'FINGERPRINT') {
                $result['fingerprint'] = (string) carr::get($part, 1);
            } elseif ($key == 'INDEX') {
                $result['hasIndex'] = ((string) carr::get($part, 1)) === '1';
            } elseif ($key == 'BRANCH') {
                $result['branch'] = (string) carr::get($part, 1);
            } elseif ($key == 'COMMIT') {
                $result['commit'] = (string) carr::get($part, 1);
            } elseif ($key == 'COMMITDATE') {
                $result['commitDate'] = (string) carr::get($part, 1);
            } elseif ($key == 'APP') {
                $appList[] = [
                    'code' => (string) carr::get($part, 1),
                    'hasBootstrap' => ((string) carr::get($part, 2)) === '1',
                    'branch' => (string) carr::get($part, 3),
                    'commit' => (string) carr::get($part, 4),
                ];
            }
        }
        $result['appList'] = $appList;

        return $result;
    }
}
