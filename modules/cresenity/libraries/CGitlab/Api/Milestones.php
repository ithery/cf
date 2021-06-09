<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 19, 2018, 5:11:25 AM
 */
class CGitlab_Api_Milestones extends CGitlab_Api {
    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     *
     * @return mixed
     */
    public function all($project_id, $page = 1, $per_page = self::PER_PAGE) {
        return $this->get($this->getProjectPath($project_id, 'milestones'), [
            'page' => $page,
            'per_page' => $per_page
        ]);
    }

    /**
     * @param int $project_id
     * @param int $milestone_id
     *
     * @return mixed
     */
    public function show($project_id, $milestone_id) {
        return $this->get($this->getProjectPath($project_id, 'milestones/' . $this->encodePath($milestone_id)));
    }

    /**
     * @param int   $project_id
     * @param array $params
     *
     * @return mixed
     */
    public function create($project_id, array $params) {
        return $this->post($this->getProjectPath($project_id, 'milestones'), $params);
    }

    /**
     * @param int   $project_id
     * @param int   $milestone_id
     * @param array $params
     *
     * @return mixed
     */
    public function update($project_id, $milestone_id, array $params) {
        return $this->put($this->getProjectPath($project_id, 'milestones/' . $this->encodePath($milestone_id)), $params);
    }

    /**
     * @param int $project_id
     * @param int $milestone_id
     *
     * @return mixed
     */
    public function issues($project_id, $milestone_id) {
        return $this->get($this->getProjectPath($project_id, 'milestones/' . $this->encodePath($milestone_id) . '/issues'));
    }
}
