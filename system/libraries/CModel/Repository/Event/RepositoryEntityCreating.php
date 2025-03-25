<?php

/**
 * Class RepositoryEntityCreated.
 */
class CModel_Repository_Event_RepositoryEntityCreating extends CModel_Repository_Event_RepositoryEventBase {
    /**
     * @var string
     */
    protected $action = 'creating';

    public function __construct(CModel_Repository_Contract_RepositoryInterface $repository, array $model) {
        parent::__construct($repository);
        $this->model = $model;
    }
}
