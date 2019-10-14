<?php

abstract class CVendor_RajaOngkir {
	
	protected $key = '9406fbe823f72dbfbe4d9b3c849c073e';
	private $android_key;
	private $ios_key;
	protected $curl;
	protected $url;

	public function __construct() {
		$this->curl = curl_init();
	}

	public function __destruct() {
		curl_close($this->curl);
	}

	public function setKey($key) {
		$this->key = $key;
	}

	public function setAndroidKey($key) {
		$this->android_key = $key;
	}

	public function setIosKey($key) {
		$this->ios_key = $key;
	}

	protected function response($res) {
		return json_decode($res);
	}

	protected function error($err) {
		return (Object) [
			'rajaongkir' => (Object) [
				'status' => (Object) [
					'code' => 'ERROR',
					'description' => $err,
				],
			],
		];
	}
}