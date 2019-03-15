<?php

/**
 * 
 */
trait CApp_Model_Trait_LogActivity
{
	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		$this->primaryKey = 'log_activity_id';
		$this->table = 'log_activity';
		$this->guarded = array('log_activity_id');
	}
}