<?php

/**
 * 
 */
interface CModel_Activity_ObserverInterface
{
	public function start($userId, string $message, CModel $logActivity);

	public function stop();

	public function created(CModel $model);

	public function updated(CModel $model);

	public function deleted(CModel $model);
}