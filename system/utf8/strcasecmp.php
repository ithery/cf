<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * CUTF8::strcasecmp
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */
function _strcasecmp($str1, $str2)
{
	if (CUTF8::is_ascii($str1) AND CUTF8::is_ascii($str2))
		return strcasecmp($str1, $str2);

	$str1 = CUTF8::strtolower($str1);
	$str2 = CUTF8::strtolower($str2);
	return strcmp($str1, $str2);
}
