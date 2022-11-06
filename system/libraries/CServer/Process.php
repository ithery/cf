<?php

class CServer_Process {
    public static function psList($grep = null) {
        $command = 'ps -ef';
        if ($grep != null) {
            $command = 'ps -ef | grep "' . $grep . '" | grep -v "grep"';
        }
        $commandResult = trim((string) shell_exec($command));
        $processes = array_filter(explode("\n", $commandResult));
        $list = [];
        if (carr::get($processes, 0) && cstr::startsWith(carr::get($processes, 0), 'UID ')) {
            $processes = array_slice($processes, 1);
        }
        foreach ($processes as $process) {
            $list[] = static::listProcess($process);
        }

        return $list;
    }

    /**
     * @param string $process
     *
     * @return \array
     */
    private static function listProcess($process) {
        $process = preg_split('/\s+/', $process, 8);

        preg_match('/\-\-port=([0-9]+)/', $process[7], $port);

        return [
            'owner' => $process[0],
            'pid' => (int) $process[1],
            'ppid' => (int) $process[2],
            'start' => $process[4],
            'time' => $process[6],
            'command' => $process[7],
        ];
    }
}
