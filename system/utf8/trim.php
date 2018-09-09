<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * CUTF8::trim
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */
function _trim($str, $charlist = NULL)
{
	if ($charlist === NULL)
		return trim($str);

	return CUTF8::ltrim(CUTF8::rtrim($str, $charlist), $charlist);
}
