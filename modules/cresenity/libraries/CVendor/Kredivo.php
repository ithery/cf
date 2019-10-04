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

	const SANDBOX_ENDPOINT = 'https://sandbox.kredivo.com/kredivo/';
	const SANDBOX_SERVERKEY = '8tLHIx8V0N6KtnSpS9Nbd6zROFFJH7';

	const PAYMENT_30_DAYS = '30_days';
	const PAYMENT_3_MONTHS = '3_months';
	const PAYMENT_6_MONTHS = '6_months';
	const PAYMENT_12_MONTHS = '12_months';
	// const PAYMENT_TYPE = [PAYMENT_30_DAYS => '30 Days', PAYMENT_3_MONTHS => '3 Months', PAYMENT_6_MONTHS => '6 Months', PAYMENT_12_MONTHS => '12 Months'];

	public function __construct($environment, $options)
	{
		if ($environment == 'dev' || $environment == 'development' || $environment == 'sandbox') {
			$this->environment = 'development';
			$this->endpoint = static::SANDBOX_ENDPOINT;
			$this->serverKey = static::SANDBOX_SERVERKEY;
		} else {
			$this->environment = $environment;
			$endpoint = carr::get($options, 'endpoint');
			$serverKey = carr::get($options, 'serverKey');

			if (! $endpoint || ! $serverKey) {
				throw new Exception('serverKey and endpoint are required');
			}
		}
	}

	private function getEndPoint() {
		return $this->endpoint;
	}

	public function checkout($options = [])
	{
		$url = $this->getEndPoint() . 'v2/checkout_url';
		$options = [];

		$client = new Client();

		try {
			$response = $client->request('POST', $url, ['form_params' => $options]);

			$this->lastResponse = json_decode($response->getBody()->getContents());
			$this->responses[] = $this->lastResponse;

			return true;
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			$this->errors[] = $this->lastError;

			return false;
		}
	}

	public function payment($options = [])
	{
		$url = $this->getEndPoint() . 'v2/payments';
		$options = [];

		$client = new Client();

		try {
			$response = $client->request('POST', $url, ['form_params' => $options]);

			$this->lastResponse = json_decode($response->getBody()->getContents());
			$this->responses[] = $this->lastResponse;

			return true;
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			$this->errors[] = $this->lastError;

			return false;
		}
	}

	public function transactionStatus()
	{
		$url = $this->getEndPoint() . 'transaction/status';
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