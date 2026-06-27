<?php
use Symfony\Component\Process\Process;

class CDaemon_Supervisor_WorkerRunner extends CDaemon_RunnerAbstract {
    /**
     * @var string
     */
    protected $supervisor;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $masterDaemonClass;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var CDaemon_Supervisor_SupervisorOptions
     */
    protected $options;

    /**
     * @param CDaemon_Supervisor_SupervisorOptions $options
     * @param string                               $masterDaemonClass
     * @param null|string                          $domain
     */
    public function __construct(CDaemon_Supervisor_SupervisorOptions $options, $masterDaemonClass, $domain = null) {
        $this->options = $options;
        $this->domain = $domain ?: CF::domain();
        $this->name = $options->workersName;
        $this->supervisor = $options->name;
        $this->masterDaemonClass = $masterDaemonClass;
    }

    /**
     * @return Process
     */
    public function run() {
        $class = CF::config('daemon.supervisor.fast_termination')
            ? CDaemon_BackgroundProcess::class
            : Process::class;
        $isUnix = CDaemon_Helper::getPlatform() === CDaemon_Helper::UNIX;
        if ($isUnix && !extension_loaded('posix')) {
            throw new Exception('posix extension is required');
        }

        if ($isUnix) {
            return $this->runUnix($class);
        } else {
            $this->runWindows();
        }
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $class
     *
     * @return Process
     */
    protected function runUnix($class = null) {
        $class = $class ?: Process::class;
        $commandToExecute = $this->getCommandToExecuteOnUnix();

        return $class::fromShellCommandline($commandToExecute, $this->options->directory ?? DOCROOT)
            ->setTimeout(null)
            ->disableOutput();
    }

    /**
     * @return string
     */
    protected function getExecutableCommand() {
        $params = $this->options->toArray();
        $params['supervisor'] = $this->supervisor;
        $params['name'] = $this->name;
        $params['alias'] = $this->alias;

        $cmd = sprintf('"%s" "%s" "%s" "%s"', 'index.php', 'cresenity/worker', $this->domain, http_build_query($params));

        return $cmd;
    }

    /**
     * @return string
     */
    protected function debugOutput() {
        $serviceClass = $this->masterDaemonClass;
        $output = DOCROOT . 'temp' . DS . 'daemon' . DS . CF::appCode() . '/' . $serviceClass . DS . $this->name . '-worker.log';
        $dir = dirname($output);
        if (!CFile::isDirectory($dir)) {
            CFile::makeDirectory($dir, 0755, true);
        }

        return $output;
    }

    /**
     * @return string
     */
    public function getLogFile() {
        return '';
    }
}
