<?php

use CModel_LogActivity_Logger as Logger;

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
		$logActivity = Logger::activity();
		$before = [];
		$after = [];
		$attributes = $model->getAttributes();

		cdbg::dd($model->properties->only('attributes', 'old'));

		foreach ($attributes as $attr) {
			$before[$attr] = $model->getOriginal($attr);
			$after[$attr] = $model->{$attr};
		}

		cdbg::dd($model);
	}

	public function deleted(CModel $model)
	{
		# code...
	}
}