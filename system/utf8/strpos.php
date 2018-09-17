<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * CUTF8::strpos
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */
function _strpos($str, $search, $offset = 0)
{
	$offset = (int) $offset;

	if (CUTF8::is_ascii($str) AND CUTF8::is_ascii($search))
		return strpos($str, $search, $offset);

	if ($offset == 0)
	{
		$array = explode($search, $str, 2);
		return isset($array[1]) ? CUTF8::strlen($array[0]) : FALSE;
	}

	$str = CUTF8::substr($str, $offset);
	$pos = CUTF8::strpos($str, $search);
	return ($pos === FALSE) ? FALSE : ($pos + $offset);
}
