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

	public static function logStart()
	{
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		if (! isset($_SESSION['CModel_LogActivity'])) {
			$_SESSION['CModel_LogActivity'] = [];
		}
	}

	public static function logEnd()
	{
		static::log();
		unset($_SESSION['CModel_LogActivity']);
	}

	public static function onLog()
	{
		return isset($_SESSION['CModel_LogActivity']);
	}

	public static function getActivities()
	{
		return carr::get($_SESSION, 'CModel_LogActivity', []);
	}

	public static function addActivity($model)
	{
		array_push($_SESSION['CModel_LogActivity'], $model);
	}

	public static function log()
	{
		cdbg::dd(static::getActivities());
	}
}