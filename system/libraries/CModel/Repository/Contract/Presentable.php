<?php

/**
 * Interface Presentable.
 */
interface CModel_Repository_Contract_Presentable {
    /**
     * @param CModel_Repository_Contract_PresenterInterface $presenter
     *
     * @return mixed
     */
    public function setPresenter(CModel_Repository_Contract_PresenterInterface $presenter);

    /**
     * @return mixed
     */
    public function presenter();
}
