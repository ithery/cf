<?php

class DigitalOcean_Factory
{
	public function __construct($options = [])
	{
		return new DigitalOcean_Client($options);
	}
}