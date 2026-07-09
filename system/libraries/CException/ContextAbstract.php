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

    /**
     * Get the latest git commit hash in the repository.
     *
     * @param string $baseDir The base directory of the git repository.
     *
     * @return string|null The latest git commit hash, or null if not found.
     */
    protected function getGitHash($baseDir) {
        return $this->command("git log --pretty=format:'%H' -n 1", $baseDir);
    }

    /**
     * Get the latest git commit message in the repository.
     *
     * @param string $baseDir The base directory of the git repository.
     *
     * @return string|null The latest git commit message, or null if not found.
     */
    protected function getGitMessage($baseDir) {
        return $this->command("git log --pretty=format:'%s' -n 1", $baseDir);
    }

    /**
     * Get the latest git tag in the repository.
     *
     * @param string $baseDir The base directory of the git repository.
     *
     * @return string|null The latest git tag, or null if no tags are found.
     */
    protected function getGitTag($baseDir) {
        return $this->command('git describe --tags --abbrev=0', $baseDir);
    }

    /**
     * Get the remote URL of the git repository.
     *
     * @param string $baseDir The base directory of the git repository.
     *
     * @return string|null The remote URL, or null if not found.
     */
    protected function getGitRemote($baseDir) {
        return $this->command('git config --get remote.origin.url', $baseDir);
    }

    /**
     * Check if the git repository is clean (no uncommitted changes).
     *
     * @param string $baseDir The base directory of the git repository.
     *
     * @return bool True if the repository is clean, false otherwise.
     */
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

    /**
     * Execute a shell command in the given base directory and return the output.
     *
     * @param string $command The shell command to execute.
     * @param string $baseDir The base directory to execute the command in.
     *
     * @return string The output of the command.
     */
    protected function command($command, $baseDir) {
        $process = Process::fromShellCommandline($command, $baseDir);

        $process->run();

        return trim($process->getOutput());
    }

    protected function getAppData() {
        $daemonClass = null;
        $isDaemon = CDaemon::isDaemon();
        $daemonService = CDaemon::getRunningService();
        if ($daemonService != null && is_object($daemonService)) {
            $daemonClass = get_class($daemonService);
        }
        $queueRunner = CQueue::runner();
        $isQueue = false;
        $queueJobName = null;
        if ($queueRunner != null) {
            $isQueue = true;
            $queueJobName = $queueRunner->getCurrentJobName();
        }

        return [
            'isCli' => CF::isCli(),
            'isCFCli' => CF::isCFCli(),
            'sharedAppCode' => CF::getSharedApp(),
            'locale' => CF::getLocale(),
            'domain' => CF::domain(),
            'appCode' => CF::appCode(),
            'orgCode' => CF::orgCode(),
            'theme' => c::theme()->getCurrentTheme(),
            'nav' => c::app()->getNavName(),
            'isDaemon' => $isDaemon,
            'daemonClass' => $daemonClass,
            'isQueue' => $isQueue,
            'queueJobName' => $queueJobName,
        ];
    }

    protected function getDebugData() {
        $variables = CDebug::getVariables();
        //serialize all variables
        return c::collect($variables)->map(function ($item) {
            if ($item instanceof Closure) {
                $item = new \Opis\Closure\SerializableClosure($item);
            }

            return serialize($item);
        })->toArray();
    }
}
