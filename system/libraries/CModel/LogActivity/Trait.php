<?php

/**
 * 
 */
trait CModel_LogActivity_Trait
{
	public static function logStart()
	{
		if (! isset($_GLOBALS['CModel_LogActivity'])) {
			$_GLOBALS['CModel_LogActivity'] = [];
		}
	}

	public static function logEnd()
	{
		$this->log();
		unset($_GLOBALS['CModel_LogActivity']);
	}

	public static function onLog()
	{
		return isset($_GLOBALS['CModel_LogActivity']);
	}

	public function getActivities()
	{
		return carr::get($_GLOBALS, 'CModel_LogActivity', []);
	}

	public function log()
	{
		
	}
}