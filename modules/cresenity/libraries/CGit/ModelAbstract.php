<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 3, 2019, 1:30:18 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CGit_ModelAbstract {

    protected $repository;

    public function getRepository() {
        return $this->repository;
    }

    public function setRepository($repository) {
        $this->repository = $repository;
        return $this;
    }

}
