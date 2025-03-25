<?php

/**
 * Interface RepositoryCriteriaInterface.
 */
interface CModel_Repository_Contract_RepositoryCriteriaInterface {
    /**
     * Push Criteria for filter the query.
     *
     * @param $criteria
     *
     * @return $this
     */
    public function pushCriteria($criteria);

    /**
     * Pop Criteria.
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria);

    /**
     * Get Collection of Criteria.
     *
     * @return CCollection
     */
    public function getCriteria();

    /**
     * Find data by Criteria.
     *
     * @param CModel_Repository_Contract_CriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CModel_Repository_Contract_CriteriaInterface $criteria);

    /**
     * Skip Criteria.
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * Reset all Criterias.
     *
     * @return $this
     */
    public function resetCriteria();
}
