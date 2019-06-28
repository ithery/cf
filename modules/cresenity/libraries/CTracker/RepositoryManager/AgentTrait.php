<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:06:31 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTracker_RepositoryManager_AgentTrait {

    /**
     *
     * @var CTracker_Repository_Agent
     */
    protected $agentRepository;

    protected function bootAgentTrait() {
        $this->agentRepository = new CTracker_Repository_Agent();
    }

    public function getCurrentAgentArray() {
        return [
            'name' => $name = $this->getCurrentUserAgent() ?: 'Other',
            'browser' => $this->userAgentParser->userAgent->family,
            'browser_version' => $this->userAgentParser->getUserAgentVersion(),
            'name_hash' => hash('sha256', $name),
        ];
    }

    public function findOrCreateAgent($data) {
        return $this->agentRepository->findOrCreate($data, ['name_hash']);
    }

    public function getAgentId() {
        return $this->findOrCreateAgent($this->getCurrentAgentArray());
    }

    public function getCurrentUserAgent() {
        return $this->userAgentParser->originalUserAgent;
    }

}
