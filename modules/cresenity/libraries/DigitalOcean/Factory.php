<?php

class DigitalOcean_Factory
{
	function __construct($options = [])
	{
		return new DigitalOcean_Client($options);
	}
}