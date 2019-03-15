<?php

use CApp_Model_Observer_LogActivity as Observer;

/**
 * 
 */
class CApp_LogActivity
{
	use CModel_Activity_ActivityTrait;

	private static $instance;
	private $isStarted;
	private $model;
	private $observer;
	private $message;

	private function __construct()
	{
		$this->isStarted = false;
		$this->model = null;
		$this->observer = Observer::class;
	}

	public static function instance()
	{
		if (! static::$instance) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	public function start($userId, string $message, CModel $model, $observer = null)
	{
		$this->isStarted = true;
		$this->message = $message;
		$this->model = $model;
		$this->observer = $observer ?: $this->observer;

		cdbg::dd($this->observer);

		if (is_string($this->observer)) {
			$this->observer = new $this->observer;
		}

		cdbg::dd($this->observer);

		static::bootLog($userId, $this->message, $this->model, $this->observer);
	}

	public function stop()
	{
		$this->isStarted = false;
		$this->observer->stop();
	}
}