<?php

use CModel_LogActivity_Observer as Observer;

/**
 * 
 */
trait CModel_LogActivity_Trait
{
	public static function bootLogActivity(CModel $model = null)
	{
		($model) ? $model->observe(Observer::class) : static::observe(Observer::class);
	}

	public static function logStart($session = null)
	{
		if (! $session) {
			$session = CSession::instance();
			if (! $session->get('CModel_LogActivity')) {
				$session->set('CModel_LogActivity', []);
			}
		}
	}

	public static function logEnd()
	{
		$session = CSession::instance();
		$session->delete('CModel_LogActivity');
	}

	public static function onLog()
	{
		$session = CSession::instance();
		return $session->get('CModel_LogActivity') !== false;
	}

	public static function getActivities()
	{
		$session = CSession::instance();
		return $session->get('CModel_LogActivity');
	}

	public static function addActivity($model)
	{
		$session = CSession::instance();
		$s = $session->get('CModel_LogActivity');
		array_push($s, $model);
		$session->set('CModel_LogActivity', $s);
	}

	public static function log()
	{
		// cdbg::dd(static::getActivities());
	}
}