<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 15, 2019, 12:10:03 PM
 */
abstract class CDaemon_WorkerAbstract implements CDaemon_WorkerInterface {
    /**
     * @var CDaemon_Worker_MediatorAbstract
     */
    protected $mediator;

    /**
     * @param CDaemon_Worker_MediatorAbstract $mediator
     */
    public function setMediator(CDaemon_Worker_MediatorAbstract $mediator) {
        $this->mediator = $this->mediator;
    }
}
