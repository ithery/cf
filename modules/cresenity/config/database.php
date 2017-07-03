<?php defined("SYSPATH") or die("No direct script access.");
/**
 * @package  Database
 *
 * Database connection settings, defined as arrays, or "groups". If no group
 * name is used when loading the database library, the group named "default"
 * will be used.
 *
 * Each group can be connected to independently, and multiple groups can be
 * connected at once.
 *
 * Group Options:
 *  benchmark     - Enable or disable database benchmarking
 *  persistent    - Enable or disable a persistent connection
 *  connection    - Array of connection specific parameters; alternatively,
 *                  you can use a DSN though it is not as fast and certain
 *                  characters could create problems (like an '@' character
 *                  in a password):
 *                  'connection'    => 'mysql://dbuser:secret@localhost/kohana'
 *  character_set - Database character set
 *  table_prefix  - Database table prefix
 *  object        - Enable or disable object results
 *  cache         - Enable or disable query caching
 *	escape        - Enable automatic query builder escaping
 */
return array(
	'default' => array (
		'benchmark'     => FALSE,
		'persistent'    => FALSE,
		'connection'    => array (
			'type'     => 'mysql',
			'user'     => 'root',
			'pass'     => 'rodex0909',
			'host'     => '192.168.1.222',
			'port'     => FALSE,
			'socket'   => FALSE,
			'database' => 'torsapi3'
		),
		'character_set' => 'utf8',
		'table_prefix'  => '',
		'object'        => TRUE,
		'cache'         => FALSE,
		'escape'        => TRUE,
	),
);
// return array(
// 'default' => array
// 	(
// 		'benchmark'     => TRUE,
// 		'persistent'    => FALSE,
// 		'connection'    => array
// 		(
// 			'type'     => 'mysql',
// 			'user'     => 'hallfami_admin',
// 			'pass'     => 'ittron2015',
// 			'host'     => 'hallfamilydb.cqatws5kitjc.ap-southeast-1.rds.amazonaws.com',
// 			'port'     => FALSE,
// 			'socket'   => FALSE,
// 			'database' => 'hallfamily_admin'
// 		),
// 		'character_set' => 'utf8',
// 		'table_prefix'  => '',
// 		'object'        => TRUE,
// 		'cache'         => FALSE,
// 		'escape'        => TRUE
// 	)
// );
