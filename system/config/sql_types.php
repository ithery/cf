<?php
defined('SYSPATH') or die('No direct access allowed.');

$config = [
    'tinyint' => ['type' => 'int', 'max' => 127],
    'smallint' => ['type' => 'int', 'max' => 32767],
    'mediumint' => ['type' => 'int', 'max' => 8388607],
    'int' => ['type' => 'int', 'max' => 2147483647],
    'integer' => ['type' => 'int', 'max' => 2147483647],
    'bigint' => ['type' => 'int', 'max' => 9223372036854775807],
    'float' => ['type' => 'float'],
    'float unsigned' => ['type' => 'float', 'min' => 0],
    'boolean' => ['type' => 'boolean'],
    'time' => ['type' => 'string', 'format' => '00:00:00'],
    'time with time zone' => ['type' => 'string'],
    'date' => ['type' => 'string', 'format' => '0000-00-00'],
    'year' => ['type' => 'string', 'format' => '0000'],
    'datetime' => ['type' => 'string', 'format' => '0000-00-00 00:00:00'],
    'timestamp with time zone' => ['type' => 'string'],
    'char' => ['type' => 'string', 'exact' => true],
    'binary' => ['type' => 'string', 'binary' => true, 'exact' => true],
    'varchar' => ['type' => 'string'],
    'varbinary' => ['type' => 'string', 'binary' => true],
    'blob' => ['type' => 'string', 'binary' => true],
    'text' => ['type' => 'string']
];

// DOUBLE
$config['double'] = $config['double precision'] = $config['decimal'] = $config['real'] = $config['numeric'] = $config['float'];
$config['double unsigned'] = $config['float unsigned'];

// BIT
$config['bit'] = $config['boolean'];

// TIMESTAMP
$config['timestamp'] = $config['timestamp without time zone'] = $config['datetime'];

// ENUM
$config['enum'] = $config['set'] = $config['varchar'];

// TEXT
$config['tinytext'] = $config['mediumtext'] = $config['longtext'] = $config['text'];

// BLOB
$config['tsvector'] = $config['tinyblob'] = $config['mediumblob'] = $config['longblob'] = $config['clob'] = $config['bytea'] = $config['blob'];

// CHARACTER
$config['character'] = $config['char'];
$config['character varying'] = $config['varchar'];

// TIME
$config['time without time zone'] = $config['time'];
