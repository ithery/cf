<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:28:33 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

trait CTracker_RepositoryManager_DomainTrait {

    /**
     *
     * @var CTracker_Repository_Domain
     */
    protected $domainRepository;

    protected function bootDomainTrait() {
        $this->domainRepository = new CTracker_Repository_Domain();
    }

    public function getDomainId() {
        return $this->domainRepository->getId();
    }

}
