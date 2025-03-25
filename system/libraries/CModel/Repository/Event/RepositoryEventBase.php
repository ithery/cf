<?php

/**
 * Class RepositoryEventBase.
 */
abstract class CModel_Repository_Event_RepositoryEventBase {
    /**
     * @var CModel
     */
    protected $model;

    /**
     * @var CModel_Repository_Contract_RepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $action;

    /**
     * @param CModel_Repository_Contract_RepositoryInterface $repository
     * @param CModel                                         $model
     */
    public function __construct(CModel_Repository_Contract_RepositoryInterface $repository, CModel $model = null) {
        $this->repository = $repository;
        $this->model = $model;
    }

    /**
     * @return Model|array
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository() {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }
}
