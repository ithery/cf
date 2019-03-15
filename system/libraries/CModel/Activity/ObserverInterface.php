<?php

/**
 * 
 */
interface CModel_Activity_ObserverInterface
{
	public function start($message = '');

	public function stop();

	public function created(CModel $model);

	public function updated(CModel $model);

	public function deleted(CModel $model);
}