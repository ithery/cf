<?php

defined('SYSPATH') or die('No direct access allowed.');

use GuzzleHttp\Client;

/**
 * 
 */
class CVendor_Zenziva
{
	private $username;
	private $password;
	private $to;
	private $message;
	private $subdomain;
	private $responses;
	private $lastResponse;
	private $errors;
	private $lastError;

	const DOMAIN = 'zenziva.net';

	public function __construct($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
		$this->subdomain = 'reguler';
	}

	public function masking()
	{
		$this->subdomain = 'alpha';
		return $this;
	}

	public function to($to)
	{
		$this->to = $to;
		return $this;
	}

	public function message($message)
	{
		$this->message = $message;
		return $this;
	}

	public function subdomain($subdomain)
	{
		$this->subdomain = $subdomain;
		return $this;
	}

	public function send($to = '', $message = '')
	{
		if (! $to) {
			$this->to($to);
		}

		if (! $message) {
			$this->message($message);
		}

		if (! $this->username) {
			throw new Exception('username has not been set');
		}

		if (! $this->password) {
			throw new Exception('passowrd has not been set');
		}

		if (! $this->to) {
			throw new Exception('to has not been set');			
		}

		if (! $this->message) {
			throw new Exception('message has not been set');			
		}

		$options = [
			'userkey' => $this->username,
			'passkey' => $this->password,
			'nohp' => $this->to,
			'pesan' => urlencode($this->message),
		];

		$client = new Client();
		$response = $client->request('POST', $this->url(), $options);

		$this->lastResponse = $response->getBody();
		$this->responses[] = $this->lastResponse;
	}

	private function url()
	{
		$path = '';

		switch ($this->subdomain) {
			case 'reguler':
			case 'alpha':
				$path = '/apps/smsapi.php';
				break;
			default:
				$path = '/api/sendsms/';
				break;
		}

		return 'https://' . $this->subdomain . '.' . static::DOMAIN . $path;
	}

	public function responses()
	{
		return $this->responses;
	}

	public function lastResponse()
	{
		return $this->lastResponse;
	}

	public function errors()
	{
		return $this->errors;
	}

	public function lastError()
	{
		return $this->lastError;
	}
}