<?php

/**
 * Description of UnitTest.
 *
 * @author Hery
 */
trait CTrait_Controller_Application_QC_Testing {
    protected function getTitle() {
        return 'Unit Test';
    }

    public function index() {
        $app = c::app();

        $app->title($this->getTitle());

        $testing = CQC::manager()->testing();

        $repository = $testing->repository();

        $divDaemon = $app->addDiv();
        $this->daemonContainer($divDaemon);
        if (!$testing->daemonIsRunning()) {
            return $app;
        }

        $tests = $repository->getTests()->toArray();

        $runTestUrl = $this->controllerUrl() . 'run';
        $pollUrl = $this->controllerUrl() . 'poll';
        $app->addView('cresenity.qc.tests', [
            'tests' => $tests,
            'runTestUrl' => $runTestUrl,
            'pollUrl' => $pollUrl,

        ]);

        return $app;
    }

    protected function daemonContainer($container = null) {
        $app = $container ?: c::app();
        /** @var CApp $app */
        $divDaemon = $app->addDiv()->addClass('mb-3');

        $testing = CQC::manager()->testing();
        $isRunning = $testing->daemonIsRunning();
        $divDaemon->setAttr('style', 'display:flex;justify-content:space-between');
        $divDaemon->addDiv()->add('Daemon Status : ' . (
            $isRunning
            ? '<span class="badge badge-success">RUNNING</span>'
            : '<span class="badge badge-danger">STOPPED</span>'
        ));
        $actionList = $divDaemon->addDiv()->addActionList();
        $action = $actionList->addAction();
        $action->setLabel($isRunning ? 'Stop' : 'Start');
        $action->addClass($isRunning ? 'btn-danger' : 'btn-success');
        $action->setConfirm();
        $action->setLink($this->controllerUrl() . 'daemon/' . ($isRunning ? 'stop' : 'start'));
        $action = $actionList->addAction();
        $action->setLabel('Log');
        $action->addClass('btn-info');
        $action->onClickListener()->addDialogHandler()
            ->setUrl($this->controllerUrl() . 'log/daemon')
            ->setSidebar();
    }

    public function daemon($method) {
        if ($method == 'start') {
            CQC::manager()->testing()->startDaemon();
        }
        if ($method == 'stop') {
            CQC::manager()->testing()->stopDaemon();
        }

        return c::redirect()->back();
    }

    public function run() {
    }

    public function poll() {
        $tests = CQC::manager()->testing()->repository()->getTests()->toArray();

        return CApp_Base::toJsonResponse(0, '', $tests);
    }

    public function log($method) {
        $app = c::app();
        if ($method == 'daemon') {
            $divLog = $app->addDiv()->addClass('console');
            $divLog->add(CQC::manager()->testing()->daemonLog());

            return $app;
        }

        return c::abort(404);
    }
}
