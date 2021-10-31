<?php

class CConsole_Command_DevSuiteCommand extends CConsole_Command {
    /**
     * @var string
     */
    protected $devSuiteCommandClass;

    /**
     * @var CDevSuite_CommandAbstract
     */
    private $devSuiteCommand;

    public function __construct() {
        if (strlen($this->devSuiteCommandClass) == 0) {
            print('no dev suite command class defined in ' . get_called_class());
            return 1;
        }
        if (!class_exists($this->devSuiteCommandClass)) {
            print('class not found :' . $this->devSuiteCommandClass);
            return 1;
        }
        $className = $this->devSuiteCommandClass;
        $this->devSuiteCommand = new $className();

        $signatureArgument = $this->devSuiteCommand->getSignatureArguments();
        if (strlen($signatureArgument) > 0) {
            $this->signature .= ' ' . $signatureArgument;
        }

        CDevSuite::bootstrap();
        parent::__construct();
    }

    protected function devSuiteCommand() {
        return $this->devSuiteCommand;
    }

    public function handle() {
        $this->devSuiteCommand()->run($this);
    }
}
