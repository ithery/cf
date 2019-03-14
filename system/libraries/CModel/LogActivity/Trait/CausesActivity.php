<?php


use Illuminate\Database\Eloquent\Relations\MorphMany;

trait CModel_LogAcivity_Trait_CausesActivity
{	
	/**
	 * [actions description]
	 *
	 * @method actions
	 *
	 * @return Illuminate\Database\Eloquent\Relations\MorphMany  [description]
	 */
    public function actions()
    {
        return $this->morphMany('CModel_LogAcivity_Model_Activity', 'causer');
    }
}
