<?php

/**
 * 
 */
class CModel_LogActivity_Observer
{
	public function created(CModel $model)
	{
		cdbg::dd($model);
	}

	public function updated(CModel $model)
	{
		# code...
	}

	public function deleted(CModel $model)
	{
		# code...
	}
}