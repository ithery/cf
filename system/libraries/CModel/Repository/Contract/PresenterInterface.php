<?php

/**
 * Interface PresenterInterface.
 */
interface CModel_Repository_Contract_PresenterInterface {
    /**
     * Prepare data to present.
     *
     * @param $data
     *
     * @return mixed
     */
    public function present($data);
}
