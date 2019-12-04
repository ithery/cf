<?php

defined('SYSPATH') or die('No direct access allowed.');

use GuzzleHttp\Client;

/**
 * 
 */
class CVendor_Kredivo
{
	private $endpoint;
	private $serverKey;
	private $responses;
	private $lastResponse;
	private $errors;
	private $lastError;
	private $environment;

	const PRODUCTION_ENDPOINT = 'https://api.kredivo.com/kredivo/';
	const SANDBOX_ENDPOINT = 'https://sandbox.kredivo.com/kredivo/';
	const SANDBOX_SERVERKEY = '8tLHIx8V0N6KtnSpS9Nbd6zROFFJH7';

	public function __construct($environment, $serverKey)
	{	
		$environment = strtolower($environment);
		$this->environment = $environment;
		$this->endpoint = static::PRODUCTION_ENDPOINT;
		$this->serverKey = $serverKey;

		if ($environment == 'dev' || $environment == 'development' || $environment == 'sandbox') {
			$this->endpoint = static::SANDBOX_ENDPOINT;
			if (! $this->serverKey) {
				$this->serverKey = static::SANDBOX_SERVERKEY;
			}
		}

		if (! $this->serverKey) {
			throw new Exception('serverKey is required');
		}
	}

	private function getEndPoint() {
		return rtrim($this->endpoint, '/') . '/';
	}

	public function checkout($options = [])
	{
		$url = $this->getEndPoint() . 'v2/checkout_url';
		$this->execute($url, 'POST', $options);
	}

	public function payment($options = [])
	{
		$url = $this->getEndPoint() . 'v2/payments';
		$this->execute($url, 'POST', $options);
	}

	public function confirm($options = [])
	{
		$url = $this->getEndPoint() . 'v2/update';
		$this->execute($url, 'GET', $options);
	}

	public function cancel($options = [])
	{
		$url = $this->getEndPoint() . 'v2/cancel_transaction';
		$this->execute($url, 'POST', $options);
	}

	public function transactionStatus($options = [])
	{
		$url = $this->getEndPoint() . 'transaction/status';
		$this->execute($url, 'POST', $options);
	}

	public function deactiveToken($options = [])
	{
		$url = $this->getEndPoint() . 'v2/deactive_user_token';
		$this->execute($url, 'POST', $options);
	}

	public function creditDetails($options = [])
	{
		$url = $this->getEndPoint() . 'v2/get_user_credit_details';
		$this->execute($url, 'POST', $options);
	}

	public function connect($options = [])
	{
		$url = $this->getEndPoint() . 'v2/connect';
		$this->execute($url, 'POST', $options);
	}

	private function execute($url, $method = 'GET', array $options = [])
	{
		$client = new Client();
		$params = [];

		switch ($method) {
			case 'POST':
				$options['server_key'] = $this->serverKey;
				$params['json'] = $options;
				break;
			default:
				$params['query'] = $options;
				break;
		}

		try {
			$response = $client->request($method, $url, $params);

			$this->lastResponse = json_decode($response->getBody()->getContents());
			$this->responses[] = $this->lastResponse;

			return true;
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			$this->errors[] = $this->lastError;

			return false;
		}
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