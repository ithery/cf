<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 19, 2018, 5:01:52 AM
 */
class CGitlab_ApiV3_ProjectNamespaces extends CGitlab_Api {
    /**
     * @param int $page
     * @param int $per_page
     *
     * @return mixed
     */
    public function all($page = 1, $per_page = self::PER_PAGE) {
        return $this->get('namespaces', [
            'page' => $page,
            'per_page' => $per_page
        ]);
    }

    /**
     * @param string $terms
     * @param int    $page
     * @param int    $per_page
     *
     * @return mixed
     */
    public function search($terms, $page = 1, $per_page = self::PER_PAGE) {
        return $this->get('namespaces', [
            'search' => $terms,
            'page' => $page,
            'per_page' => $per_page
        ]);
    }
}
