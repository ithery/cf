<?php

/**
 * 
 */
trait CModel_LogActivity_Trait
{
	public static function logStart()
	{
		if (! isset($GLOBALS['CModel_LogActivity'])) {
			$GLOBALS['CModel_LogActivity'] = [];
		}
	}

	public static function logEnd()
	{
		$this->log();
		unset($GLOBALS['CModel_LogActivity']);
	}

	public static function onLog()
	{
		return isset($GLOBALS['CModel_LogActivity']);
	}

	public static function getActivities()
	{
		return carr::get($GLOBALS, 'CModel_LogActivity', []);
	}

	public function push(CModel $model)
	{
		array_push($GLOBALS['CModel_LogActivity'], $model);
	}

	public function log()
	{
		
	}
}