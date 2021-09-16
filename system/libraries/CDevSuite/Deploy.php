<?php

/**
 * Description of Deploy
 *
 * @author Hery
 */

use Symfony\Component\Process\Process;

class CDevSuite_Deploy {
    /**
     * The hosts that have already been assigned a color for output.
     *
     * @var array
     */
    protected $hostsWithColor = [];

    /**
     * @var CDevSuite_Filesystem
     */
    protected $files;

    public function __construct() {
        $this->files = CDevSuite::filesystem();
    }

    public function path() {
        $dir = CF::appDir();
        return $dir;
    }

    public function deployFile() {
        return c::fixPath($this->path()) . 'deploy.blade.php';
    }

    public function deployFileExistsOrExit() {
        if (!$this->files->exists($this->deployFile())) {
            CDevSuite::error("{$this->deployFile()} not found.\n");

            exit(CConsole::FAILURE_EXIT);
        }
    }

    public function init($host) {
        if (!$this->files->exists($this->deployFile())) {
            $this->files->putAsUser($this->deployFile(), "@servers(['web' => '" . $host . "'])
@task('deploy')
    cd /path/to/site
    git pull origin master
@endtask
");
        }
    }

    public function run($taskName, $continue, $pretending) {
        $container = $this->loadTaskContainer();

        $tasks = [$taskName];

        if ($macro = $container->getMacro($taskName)) {
            $tasks = $macro;
        }

        $exitCode = CConsole::SUCCESS_EXIT;

        foreach ($tasks as $task) {
            $thisCode = $this->runTask($container, $task, $taskName, $pretending);

            if (0 !== $thisCode) {
                $exitCode = $thisCode;
            }

            if ($thisCode > 0 && !$continue) {
                CDevSuite::error('This task did not complete successfully on one of your servers');

                break;
            }
        }

        foreach ($container->getFinishedCallbacks() as $callback) {
            call_user_func($callback);
        }

        return $exitCode;
    }

    /**
     * Load the task container instance with the Envoy file.
     *
     * @return CDevSuite_TaskContainer
     */
    protected function loadTaskContainer() {
        c::with($container = new CDevSuite_Deploy_TaskContainer)->load(
            $this->deployFile(),
            new CDevSuite_Deploy_Compiler,
            $this->getOptions()
        );

        return $container;
    }

    /**
     * Gather the dynamic options for the command.
     *
     * @return array
     */
    protected function getOptions() {
        $options = [];

        // Here we will gather all of the command line options that have been specified with
        // the double hyphens in front of their name. We will make these available to the
        // Blade task file so they can be used in echo statements and other structures.
        foreach ($_SERVER['argv'] as $argument) {
            if (!cstr::startsWith($argument, '--') || in_array($argument, $this->ignoreOptions)) {
                continue;
            }

            $option = explode('=', substr($argument, 2), 2);

            if (count($option) == 1) {
                $option[1] = true;
            }

            $options[$option[0]] = $option[1];
        }

        return $options;
    }

    /**
     * Run the given task out of the container.
     *
     * @param CDevSuite_Deploy_TaskContainer $container
     * @param string                         $task
     * @param mixed                          $taskName
     * @param mixed                          $pretending
     *
     * @return null|int|void
     */
    protected function runTask($container, $task, $taskName, $pretending) {
        $macroOptions = $container->getMacroOptions($taskName);

        $confirm = $container->getTask($task, $macroOptions)->confirm;

        if ($confirm && !$this->confirmTaskWithUser($task, $confirm)) {
            return;
        }

        if (($exitCode = $this->runTaskOverSSH($container->getTask($task, $macroOptions), $pretending)) > 0) {
            foreach ($container->getErrorCallbacks() as $callback) {
                call_user_func($callback, $task);
            }

            return $exitCode;
        }

        foreach ($container->getAfterCallbacks() as $callback) {
            call_user_func($callback, $task);
        }
    }

    /**
     * Run the given task and return the exit code.
     *
     * @param CDevSuite_Deploy_Task $task
     * @param mixed                 $pretending
     *
     * @return int
     */
    protected function runTaskOverSSH(CDevSuite_Deploy_Task $task, $pretending) {
        // If the pretending option has been set, we'll simply dump the script out to the command
        // line so the developer can inspect it which is useful for just inspecting the script
        // before it is actually run against these servers. Allows checking for errors, etc.
        if ($pretending) {
            echo $task->script . PHP_EOL;

            return 1;
        } else {
            return $this->passToRemoteProcessor($task);
        }
    }

    /**
     * Run the given task and return the exit code.
     *
     * @param CDevSuite_Deploy_Task $task
     *
     * @return int
     */
    protected function passToRemoteProcessor(CDevSuite_Deploy_Task $task) {
        return $this->getRemoteProcessor($task)->run($task, function ($type, $host, $line) {
            if (cstr::startsWith($line, 'Warning: Permanently added ')) {
                return;
            }

            $this->displayOutput($type, $host, $line);
        });
    }

    /**
     * Display the given output line.
     *
     * @param int    $type
     * @param string $host
     * @param string $line
     *
     * @return void
     */
    protected function displayOutput($type, $host, $line) {
        $lines = explode("\n", $line);

        $hostColor = $this->getHostColor($host);

        foreach ($lines as $line) {
            if (strlen(trim($line)) === 0) {
                continue;
            }

            if ($type == Process::OUT) {
                CDevSuite::output($hostColor . ': ' . trim($line));
            } else {
                CDevSuite::output($hostColor . ':  ' . '<fg=red>' . trim($line) . '</>');
            }
        }
    }

    /**
     * Return the hostname wrapped in a color tag.
     *
     * @param string $host
     *
     * @return string
     */
    protected function getHostColor($host) {
        $colors = ['yellow', 'cyan', 'magenta', 'blue'];

        if (!in_array($host, $this->hostsWithColor)) {
            $this->hostsWithColor[] = $host;
        }

        $color = $colors[array_search($host, $this->hostsWithColor) % count($colors)];

        return "<fg={$color}>[{$host}]</>";
    }

    /**
     * Get the SSH processor for the task.
     *
     * @param CDevSuite_Deploy_Task $task
     *
     * @return CDevSuite_Deploy_RemoteProcessor
     */
    protected function getRemoteProcessor(CDevSuite_Deploy_Task $task) {
        return $task->parallel ? new CDevSuite_Deploy_ParallelSSH : new CDevSuite_Deploy_SSH;
    }
}
