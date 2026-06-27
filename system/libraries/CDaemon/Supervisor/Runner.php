<?php
use Symfony\Component\Process\Process;

class CDaemon_Supervisor_Runner extends CDaemon_RunnerAbstract {
    /**
     * @var CDaemon_Supervisor_MasterSupervisor
     */
    protected $master;

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
     * @param CDaemon_Supervisor_MasterSupervisor  $master
     * @param CDaemon_Supervisor_SupervisorOptions $options
     * @param null|string                          $domain
     */
    public function __construct(CDaemon_Supervisor_MasterSupervisor $master, CDaemon_Supervisor_SupervisorOptions $options, $domain = null) {
        $this->master = $master;
        $this->options = $options;
        $this->domain = $domain ?: CF::domain();
        $this->name = $options->name;
        $this->masterDaemonClass = $master->getDaemonClass();
        $this->alias = $this->masterDaemonClass ? $this->masterDaemonClass . '-' . $this->name : $this->name;
    }

    /**
     * @return Process
     */
    public function run() {
        $isUnix = CDaemon_Helper::getPlatform() === CDaemon_Helper::UNIX;
        if ($isUnix && !extension_loaded('posix')) {
            throw new Exception('posix extension is required');
        }

        if ($isUnix) {
            return $this->runUnix();
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
     * @return Process
     */
    protected function runUnix() {
        $commandToExecute = $this->getCommandToExecuteOnUnix();

        return Process::fromShellCommandline($commandToExecute, $this->options->directory ?? DOCROOT)
            ->setTimeout(null)
            ->disableOutput();
    }

    /**
     * @return string
     */
    protected function getExecutableCommand() {
        $params = $this->options->toArray();
        $params['masterDaemonClass'] = $this->masterDaemonClass;
        $params['alias'] = $this->alias;

        $cmd = sprintf('"%s" "%s" "%s" "%s"', 'index.php', 'cresenity/supervisor', $this->domain, http_build_query($params));

        return $cmd;
    }

    /**
     * @return string
     */
    protected function debugOutput() {
        $serviceClass = $this->master->getDaemonClass();
        $output = DOCROOT . 'temp' . DS . 'daemon' . DS . CF::appCode() . '/' . $serviceClass . DS . $this->name . '.log';
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
