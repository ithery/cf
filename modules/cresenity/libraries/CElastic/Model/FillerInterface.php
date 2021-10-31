<?php

//namespace Sleimanx2\Plastic\Fillers;
//
//use Illuminate\Database\Eloquent\Model;
//use Sleimanx2\Plastic\PlasticResult as  Result;

interface CElastic_Model_FillerInterface
{
    /**
     * Fill the results hists into Model.
     *
     * @param Model  $model
     * @param Result $result
     *
     * @return mixed
     */
    public function fill(CElastic_Model $model, CElastic_Result $result);
}
