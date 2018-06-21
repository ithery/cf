<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 5:01:52 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CGitlab_ApiV3_ProjectNamespaces extends CGitlab_Api {

    /**
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function all($page = 1, $per_page = self::PER_PAGE) {
        return $this->get('namespaces', array(
                    'page' => $page,
                    'per_page' => $per_page
        ));
    }

    /**
     * @param string $terms
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function search($terms, $page = 1, $per_page = self::PER_PAGE) {
        return $this->get('namespaces', array(
                    'search' => $terms,
                    'page' => $page,
                    'per_page' => $per_page
        ));
    }

}
