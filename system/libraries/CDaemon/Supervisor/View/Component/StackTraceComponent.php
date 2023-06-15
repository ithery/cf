<?php
class CDaemon_Supervisor_View_Component_StackTraceComponent extends CView_ComponentAbstract {
    public $trace;

    public $uniqid;

    public function __construct(array $trace = null) {
        $this->trace = $trace;
        $this->uniqid = uniqid();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return string
     */
    public function render() {
        return c::view('cresenity.daemon.supervisor.component.stack-trace', [
            'trace' => $this->trace ?: '',
            'uniqid' => $this->uniqid ?: ''
        ]);
    }
}
