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
		$after = $model->getAttributes();
		$changes = [];

		$logActivity
			->type('create')
			->before($before)
			->after($after)
			->changes($changes)
			->log($model->getTable() . ' [' . $model->getKey() . '] Created');
	}

	public function updated(CModel $model)
	{
		$logActivity = Logger::activity();
		$before = [];
		$after = $model->getAttributes();
		$changes = $model->getDirty();

		foreach ($after as $key => $value) {
			$before[$key] = $model->getOriginal($key);
		}

		$logActivity
			->type('update')
			->before($before)
			->after($after)
			->changes($changes)
			->log($model->getTable() . ' [' . $model->getKey() . '] Updated');
	}

	public function deleted(CModel $model)
	{
		$logActivity = Logger::activity();
		$before = [];
		$after = $model->getAttributes();
		$changes = $model->getDirty();

		foreach ($after as $key => $value) {
			$before[$key] = $model->getOriginal($key);
		}

		$logActivity
			->type('delete')
			->before($before)
			->after($after)
			->changes($changes)
			->log($model->getTable() . ' [' . $model->getKey() . '] Deleted');
	}
}