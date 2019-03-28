<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * 
 */
class CCollector
{
	const EXT = '.txt';
	const DEPRECATED = 'deprecated';
	const EXCEPTION = 'exception';
	const PROFILER = 'profiler';
	const TYPE = ['deprecated', 'exception', 'profiler'];

	public static function getDirectory()
	{
		$path = DOCROOT . 'temp' . DS;
		if (! is_dir($path)) {
			mkdir($path, 0777, true);
		}

		$path .= 'collector' . DS;
		if (! is_dir($path)) {
			mkdir($path, 0777, true);
		}

		return $path;
	}

	public static function get($type = null, $dateStart = null, $dateEnd = null)
	{
		$dateStart = $dateStart ? new DateTime($dateStart) : new DateTime('000000');
		$dateEnd = $dateEnd ? new DateTime($dateEnd) : new DateTime('000000');
		$path = static::getDirectory();
		$data = [];

		if ($type && strtolower($type) != 'all') {
			if (! in_array($type, static::TYPE)) {
				throw new CException("Type $type is not found");
			}

			$tempPath = $path . DS . $type . DS;
			foreach (glob($tempPath . '*' . static::EXT) as $file) {
				$date = pathinfo($file)['filename'];
				$dateTime = new DateTime($date);
				if ($dateTime >= $dateStart && $dateTime <= $dateEnd) {
					$data[$date] = static::getContent($file);
				}
			}
		} else {
			foreach (static::TYPE as $type) {
				$tempPath = $path . DS . $type . DS;
				$data[$type] = [];
				foreach (glob($tempPath . '*' . static::EXT) as $file) {
					$date = pathinfo($file)['filename'];
					$dateTime = new DateTime($date);
					if ($dateTime >= $dateStart && $dateTime <= $dateEnd) {
						$data[$type][$date] = static::getContent($file);
					}
				}
			}
		}

		return $data;
	}

	private static function getContent($path) {
		$data = [];
		if (file_exists($path)) {
			$content = file($path);
			$data = array_map(function($data) {
				return json_decode($data);
			}, $content);
		}
		return $data;
	}

	public static function put($type, $data)
	{
		if (! in_array($type, static::TYPE)) {
			throw new CException("Type $type is not found");
		}

		json_decode($data);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$data = json_encode($data);
		}

		$path = static::getDirectory();
		$path .= $type . DS;
		if (! is_dir($path)) {
			mkdir($path, 0777, true);
		}
		$path .= date('Ymd') . static::EXT;
		file_put_contents($path, $data . PHP_EOL, FILE_APPEND | LOCK_EX);

		return true;
	}

	public static function deprecated(Exception $exception)
	{
		$data = static::getDataFromException($exception);
		static::put(static::DEPRECATED, $data);
	}

	public static function exception(Exception $exception)
	{
		$data = static::getDataFromException($exception);
		static::put(static::EXCEPTION, $data);
	}

	public static function profiler()
	{
		static::put(static::PROFILER, $data);
	}

	private static function getDataFromException(Exception $exception)
	{
		$app = CApp::instance();

		$error = get_class($exception);
		$message = $exception->getMessage();
		$file = $exception->getFile();
		$line = $exception->getLine();
		$trace = $exception->getTrace();

		$data = [];
		$data['datetime'] = date('Y-m-d H:i:s');
		$data['appId'] = $app->appId();
		$data['appCode'] = $app->code();
		$data['admin'] = $app->admin();
		$data['member'] = $app->member();
		$data['user'] = $app->user();
		$data['role'] = $app->role();
		$data['org'] = $app->org();
		$data['orgId'] = $app->orgId();
		$data['error'] = $error;
		$data['message'] = $message;
		$data['file'] = $file;
		$data['line'] = $line;
		$data['trace'] = $trace;
		$data['description'] = '';

		return $data;
	}
}