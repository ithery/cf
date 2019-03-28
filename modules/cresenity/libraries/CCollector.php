<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * 
 */
class CCollector
{
	const EXT = '.txt';
	const TYPE = [
		'deprecated',
		'exception',
		'profiler',
	];

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

	public static function get($type = null)
	{
		$path = static::getDirectory();
		$data = [];

		if ($type) {
			if (! in_array($type, static::TYPE)) {
				throw new CException("Type $type is not found");
			}

			$tempPath = $path . DS . $type . DS;
			foreach (glob($tempPath . '*' . static::EXT) as $file) {
				$date = pathinfo($file)['filename'];
				$data[$date] = static::getContent($file);
			}
		} else {
			foreach (static::TYPE as $type) {
				$tempPath = $path . DS . $type . DS;
				$data[$type] = [];
				foreach (glob($tempPath . '*' . static::EXT) as $file) {
					$date = pathinfo($file)['filename'];
					$data[$type][$date] = static::getContent($file);
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
		static::put('deprecated', $data);
	}
}