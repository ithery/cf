<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 5:08:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
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
     * @return mixed
     */
    public function create($url) {
        return $this->post('hooks', array(
                    'url' => $url
        ));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function test($id) {
        return $this->get('hooks/' . $this->encodePath($id));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function remove($id) {
        return $this->delete('hooks/' . $this->encodePath($id));
    }

}
