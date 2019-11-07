<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 7, 2019, 11:13:39 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_MongoDB_SoftDelete_SoftDeleteTrait {

    use CModel_SoftDelete_SoftDeleteTrait;

    /**
     * Get the fully qualified "status" column.
     *
     * @return string
     */
    public function getQualifiedStatusColumn() {
        return $this->getStatusColumn();
    }

}
