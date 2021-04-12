<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 19, 2018, 5:08:31 AM
 */
class CGitlab_Api_SystemHooks extends CGitlab_Api {
    /**
     * @return mixed
     */
    public function all() {
        return $this->get('hooks');
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    public function create($url) {
        return $this->post('hooks', [
            'url' => $url
        ]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function test($id) {
        return $this->get('hooks/' . $this->encodePath($id));
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function remove($id) {
        return $this->delete('hooks/' . $this->encodePath($id));
    }
}
