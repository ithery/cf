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

			$tempPath = $path . $type . static::EXT;
			if (file_exists($tempPath)) {
				$content = file($tempPath);
				$data = $content;
			}
		} else {
			foreach (static::TYPE as $type) {
				$tempPath = $path . $type . static::EXT;
				if (file_exists($tempPath)) {
					$content = file_get_contents($tempPath);
					$content = preg_split('/\r\n/', $content);
					$data[$type] = $content;
				}
			}
		}

		return $data;
	}

	public static function put($type, $data)
	{
		if (! in_array($type, static::TYPE)) {
			throw new CException("Type $type is not found");
		}

		$path = static::getDirectory() . $type . static::EXT;
		file_put_contents($path, $data . PHP_EOL, FILE_APPEND | LOCK_EX);

		return true;
	}
}