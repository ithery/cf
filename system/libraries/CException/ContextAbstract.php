<?php
use Symfony\Component\Process\Process;

abstract class CException_ContextAbstract {
    public function getGit() {
        $baseDir = $this->getGitBaseDirectory();

        if (!$baseDir) {
            return [];
        }

        $git = [
            'hash' => $this->getGitHash($baseDir),
            'message' => $this->getGitMessage($baseDir),
            'tag' => $this->getGitTag($baseDir),
            'remote' => $this->getGitRemote($baseDir),
            'isDirty' => !$this->getGitIsClean($baseDir),
        ];

        return $git;
    }

    protected function getGitHash($baseDir) {
        return $this->command("git log --pretty=format:'%H' -n 1", $baseDir);
    }

    protected function getGitMessage($baseDir) {
        return $this->command("git log --pretty=format:'%s' -n 1", $baseDir);
    }

    protected function getGitTag($baseDir) {
        return $this->command('git describe --tags --abbrev=0', $baseDir);
    }

    protected function getGitRemote($baseDir) {
        return $this->command('git config --get remote.origin.url', $baseDir);
    }

    protected function getGitIsClean($baseDir) {
        return empty($this->command('git status -s', $baseDir));
    }

    protected function getGitBaseDirectory() {
        /** @var Process $process */
        $process = Process::fromShellCommandline('echo $(git rev-parse --show-toplevel)');

        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $directory = trim($process->getOutput());

        if (!file_exists($directory)) {
            return null;
        }

        return $directory;
    }

    protected function command($command, $baseDir) {
        $process = Process::fromShellCommandline($command, $baseDir);

        $process->run();

        return trim($process->getOutput());
    }
}
