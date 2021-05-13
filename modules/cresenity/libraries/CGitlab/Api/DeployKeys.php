<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 19, 2018, 5:13:41 AM
 */
class CGitlab_Api_DeployKeys extends CGitlab_Api {
    const ORDER_BY = 'id';

    const SORT = 'asc';

    /**
     * @param int    $page
     * @param int    $per_page
     * @param string $order_by
     * @param string $sort
     *
     * @return mixed
     */
    public function all($page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT) {
        return $this->get('deploy_keys', [
            'page' => $page,
            'per_page' => $per_page,
            'order_by' => $order_by,
            'sort' => $sort
        ]);
    }
}
