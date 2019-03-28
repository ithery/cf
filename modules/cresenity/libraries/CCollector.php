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
		$dateStart = $dateStart ? new DateTime($dateStart) : new DateTime();
		$start = new DateTime($dateStart->format('Ymd'));
		$dateEnd = $dateEnd ? new DateTime($dateEnd) : new DateTime();
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
				if ($dateTime >= $start && $dateTime <= $dateEnd) {
					$data = static::getContent($file, $dateStart, $dateEnd);
				}
			}
		} else {
			foreach (static::TYPE as $type) {
				$tempPath = $path . DS . $type . DS;
				$data[$type] = [];
				foreach (glob($tempPath . '*' . static::EXT) as $file) {
					$date = pathinfo($file)['filename'];
					$dateTime = new DateTime($date);
					if ($dateTime >= $start && $dateTime <= $dateEnd) {
						$data[$type] = static::getContent($file, $dateStart, $dateEnd);
					}
				}
			}
		}

		return $data;
	}

	private static function getContent($path, $dateStart, $dateEnd) {
		$data = [];
		if (file_exists($path)) {
			$content = file($path);
			$data = array_map(function($data) {
				return json_decode($data);
			}, $content);
			$data = array_filter($data, function($data) use ($dateStart, $dateEnd) {
				$date = new DateTime($data->datetime);
				return ($date >= $dateStart && $date <= $dateEnd);
			});
		}
		return $data;
	}

	public static function put($type, $data)
	{
		if (! in_array($type, static::TYPE)) {
			throw new CException("Type $type is not found");
		}

		if (! is_string($data)) {
			$data = json_encode($data);
		}

		json_decode($data);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new CException(json_last_error_msg());
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

	public static function getElasticServer()
	{
		$serverElasticId = 2; // Dev Elastic
		try {
		    $serverElasticModel = DModel::make('ServerElastic')->findOrFail($serverElasticId);
		    $elastic = DElastic::getElasticFromModel($serverElasticModel);
		    $client = $elastic->createClient();
		    $elasticType = $client->getIndex('collector')->getType('collector');

		    return $elasticType;
		} catch (Exception $e) {
		    return false;
		}
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
		$trace = CF::backtrace($trace);

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

		return json_encode($data);
	}
}