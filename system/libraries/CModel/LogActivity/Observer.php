<?php

use CModel_LogActivity_Logger as Logger;

/**
 * 
 */
class CModel_LogActivity_Observer
{
	public function created(CModel $model)
	{
		$logActivity = Logger::activity();
		$before = [];
		$after = [];
		$attributes = $model->getAttributes();

		foreach ($attributes as $attr) {
			$after[$attr] = $model->{$attr};
		}

		$logActivity
			->type('create')
			->before($before)
			->after($after);

		cdbg::dd($model);
	}

	public function updated(CModel $model)
	{
		$logActivity = Logger::activity();
		$before = [];
		$after = [];
		$attributes = $model->getAttributes();

		foreach ($attributes as $attr) {
			$before[$attr] = $model->getOriginal($attr);
			$after[$attr] = $model->{$attr};
		}

		$logActivity
			->type('update')
			->before($before)
			->after($after);

		cdbg::dd(get_class_methods($model));
	}

	public function deleted(CModel $model)
	{
		$logActivity = Logger::activity();
		$before = [];
		$after = [];
		$attributes = $model->getAttributes();

		foreach ($attributes as $attr) {
			$before[$attr] = $model->getOriginal($attr);
			$after[$attr] = $model->{$attr};
		}

		$logActivity
			->type('delete')
			->before($before)
			->after($after);

		cdbg::dd(get_class_methods($model));
	}
}