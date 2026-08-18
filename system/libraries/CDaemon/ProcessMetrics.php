<?php

/**
 * File descriptor usage, process age, and CPU time for an arbitrary pid -
 * a plain function of a pid (no daemon/service coupling) specifically so
 * it can be tested against a real /proc entry (e.g. the test process's own
 * getmypid()) without needing an actual daemon running. See
 * CDaemon_Runner::getProcessMetrics() for the daemon-service-aware caller.
 *
 * `fdLimit` reads /proc/<pid>/limits rather than posix_getrlimit(), which
 * only ever reports the *calling* process's own limit - useless here since
 * the intended caller is a web request inspecting a different, already-
 * running process. Age and CPU time come from one `ps` call (etimes/time
 * columns) rather than parsing /proc/<pid>/stat's clock-tick columns,
 * matching the shell_exec()-a-`ps`-column approach
 * CDaemon_Runner::getStartTime() already uses elsewhere in this class
 * family. Linux (or any /proc-having Unix) only - returns null outright
 * on a platform without /proc.
 */
class CDaemon_ProcessMetrics {
    /**
     * @param int|string $pid
     *
     * @return null|array{fdUsed: null|int, fdLimit: null|int, ageSeconds: null|int, cpuSeconds: null|float, cpuRatio: null|float}
     */
    public static function forPid($pid) {
        if (!is_dir('/proc')) {
            return null;
        }

        $pid = (int) $pid;

        $metrics = [
            'fdUsed' => null,
            'fdLimit' => null,
            'ageSeconds' => null,
            'cpuSeconds' => null,
            'cpuRatio' => null,
        ];

        $fdDir = "/proc/{$pid}/fd";
        if (is_dir($fdDir) && is_readable($fdDir)) {
            $entries = @scandir($fdDir);
            if ($entries !== false) {
                // drop the "." and ".." entries scandir() always includes
                $metrics['fdUsed'] = count($entries) - 2;
            }
        }

        $limitsFile = "/proc/{$pid}/limits";
        if (is_readable($limitsFile)) {
            $limitsContents = @file_get_contents($limitsFile);
            if ($limitsContents && preg_match('/Max open files\s+(\d+|unlimited)/', $limitsContents, $limitsMatch)) {
                $metrics['fdLimit'] = $limitsMatch[1] === 'unlimited' ? null : (int) $limitsMatch[1];
            }
        }

        $psOutput = trim((string) shell_exec('ps -o etimes=,time= -p ' . $pid . ' 2>/dev/null'));
        if ($psOutput && preg_match('/^(\d+)\s+(?:(\d+)-)?(\d+):(\d+):(\d+)$/', $psOutput, $psMatch)) {
            $metrics['ageSeconds'] = (int) $psMatch[1];
            $cpuDays = (int) ($psMatch[2] ?: 0);
            $metrics['cpuSeconds'] = $cpuDays * 86400 + ((int) $psMatch[3]) * 3600 + ((int) $psMatch[4]) * 60 + (int) $psMatch[5];

            if ($metrics['ageSeconds'] > 0) {
                $metrics['cpuRatio'] = round($metrics['cpuSeconds'] / $metrics['ageSeconds'], 4);
            }
        }

        return $metrics;
    }

    /**
     * Whether a Redis-registered CDaemon_Supervisor master/supervisor name
     * belongs to this host. Names are "<hostname-slug>-<token>"
     * (CDaemon_Supervisor_MasterSupervisor::name()); a supervisor's own
     * "master" field is the same string minus its trailing segment. A pid
     * registered by a different host is only meaningful read on that host -
     * forPid() here would report whatever unrelated local process happens to
     * have that pid - so callers gate forPid() on this first. Requires an
     * exact "-" boundary after the hostname slug so a host named "web" does
     * not match a name belonging to "web2".
     *
     * @param string $name         the master/supervisor name, or a supervisor's "master" field
     * @param string $hostBasename CDaemon_Supervisor_MasterSupervisor::basename() for this host
     *
     * @return bool
     */
    public static function belongsToHost($name, $hostBasename) {
        return cstr::startsWith($name, $hostBasename . '-');
    }

    /**
     * Human-readable summary used by both the sysadmin daemon table and
     * the CDaemon_Supervisor dashboard, so the two surfaces don't grow
     * two slightly different renderings of the same numbers.
     *
     * @param null|array $metrics forPid()'s return value
     *
     * @return array{fd: string, age: string, cpu: string}
     */
    public static function format($metrics) {
        if ($metrics === null) {
            return ['fd' => '-', 'age' => '-', 'cpu' => '-'];
        }

        $fd = $metrics['fdUsed'] === null
            ? '-'
            : $metrics['fdUsed'] . ($metrics['fdLimit'] !== null ? ' / ' . $metrics['fdLimit'] : '');

        $age = $metrics['ageSeconds'] === null ? '-' : self::formatDuration($metrics['ageSeconds']);

        $cpu = $metrics['cpuSeconds'] === null
            ? '-'
            : self::formatDuration((int) $metrics['cpuSeconds']) . ($metrics['cpuRatio'] !== null ? ' (' . round($metrics['cpuRatio'] * 100, 1) . '%)' : '');

        return ['fd' => $fd, 'age' => $age, 'cpu' => $cpu];
    }

    /**
     * @param int $seconds
     *
     * @return string
     */
    private static function formatDuration($seconds) {
        if ($seconds < 60) {
            return $seconds . 's';
        }
        if ($seconds < 3600) {
            return floor($seconds / 60) . 'm ' . ($seconds % 60) . 's';
        }
        if ($seconds < 86400) {
            return floor($seconds / 3600) . 'h ' . floor(($seconds % 3600) / 60) . 'm';
        }

        return floor($seconds / 86400) . 'd ' . floor(($seconds % 86400) / 3600) . 'h';
    }
}
