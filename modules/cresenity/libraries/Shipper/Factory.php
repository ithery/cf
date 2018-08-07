<?php

namespace Shipper;

class Factory {
	static function create($env = 'production') {
		return new Shipper($env);
	}
}