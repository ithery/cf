<?php

/**
 * Interface CriteriaInterface.
 */
interface CModel_Repository_Contract_CriteriaInterface {
    /**
     * Apply criteria in query repository.
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, CModel_Repository_Contract_RepositoryInterface $repository);
}
