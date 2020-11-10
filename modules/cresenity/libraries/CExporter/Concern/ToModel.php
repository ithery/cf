<?php

/**
 * Description of ToModel
 *
 * @author Hery
 */
interface CExporter_Concern_ToModel {

    /**
     * @param array $row
     *
     * @return CModel|CModel[]|null
     */
    public function model(array $row);
}
