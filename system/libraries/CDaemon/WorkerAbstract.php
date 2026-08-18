<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CDaemon_WorkerAbstract implements CDaemon_WorkerInterface {
    /**
     * @var CDaemon_Worker_MediatorAbstract
     */
    protected $mediator;

    /**
     * @param CDaemon_Worker_MediatorAbstract $mediator
     */
    public function setMediator(CDaemon_Worker_MediatorAbstract $mediator) {
        $this->mediator = $mediator;
    }
}
