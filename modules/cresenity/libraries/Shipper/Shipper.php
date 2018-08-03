<?php

namespace Shipper;

class Shipper {
	private $key = 'c46eacd847a2ab8d4459d3e54c8694dc';
	private $url;
	private $curl;
	private $environment;

	public function __construct($env = 'production') {
		$this->environment = $env;
		$this->url = 'https://api.shipper.id/prod/';
		if ($this->environment == 'dev' || $this->environment == 'development') {
			$this->url = 'https://api.shipper.id/sandbox/';
		}
		$this->curl = curl_init();
	}

	public function __destruct() {
		curl_close($this->curl);
	}

	public function setKey($key) {
		$this->key = $key;
	}

	public function getMerchants($phone = '') {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
		];

		if ($phone) {
			$options['phone'] = $phone;
		}

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/merchants?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function updateMerchant($merchantId) {
		$method = 'PUT';
		$options = [
			'apiKey' => $this->key,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/merchants/' . $merchantId . '?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function subscription($activation, $merchantLogo = '', $merchantAds = '') {
		$method = 'PUT';
		$options = [
			'apiKey' => $this->key,
			'customAWB' => $activation,
			'merchantLogo' => $merchantLogo,
			'merchantAds' => $merchantAds
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/subscriptions?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getCountries() {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/countries?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getProvinces() {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/provinces?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getCities($provinceId = 'all') {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
		];

		if ($provinceId == 'all') {
			$options['origin'] = $provinceId;
		} elseif ($provinceId) {
			$options['province'] = $provinceId;
		}

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/cities?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getSuburbs($cityId) {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
			'city' => $cityId,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/suburbs?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getAreas($suburbId) {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
			'suburb' => $suburbId,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/areas?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function search($value = '') {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/details/' . $value . '?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getDomesticRates(
		$origin
		,$destination
		,$weight
		,$length
		,$width
		,$height
		,$value
		,$type
		,$cod
		,$order
	) {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
			'o' => $origin,
			'd' => $destination,
			'wt' => $weight,
			'l' => $length,
			'w' => $width,
			'h' => $height,
			'v' => $value,
			'type' => $type,
			'cod' => $cod,
			'order' => $order,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/domesticRates?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getInternationalRates(
		$destination
		,$weight
		,$length
		,$width
		,$height
		,$value
		,$type
		,$order
	) {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
			'd' => $destination,
			'wt' => $weight,
			'l' => $length,
			'w' => $width,
			'h' => $height,
			'v' => $value,
			'type' => $type,
			'order' => $order,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/intlRates?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function domesticOrder(
		$origin
		,$destination
		,$weight
		,$length
		,$width
		,$height
		,$value
		,$rateId
		,$consignerName
		,$consignerPhoneNumber
		,$originAddress
		,$originDirection
		,$consigneeName
		,$consigneePhoneNumber
		,$destinationAddress
		,$destinationDirection
		,$itemName
		,$contents
		,$useInsurance
		,$packageType
		,$externalId
		,$paymentType
		,$cod
	) {
		$method = 'POST';
		$options = [
			'apiKey' => $this->key,
			'o' => $origin,
			'd' => $destination,
			'wt' => $weight,
			'l' => $length,
			'w' => $width,
			'h' => $height,
			'v' => $value,
			'consignerName' => $consignerName,
			'consignerPhoneNumber' => $consignerPhoneNumber,
			'originAddress' => $originAddress,
			'originDirection' => $originDirection,
			'consigneeName' => $consigneeName,
			'consigneePhoneNumber' => $consigneePhoneNumber,
			'destinationAddress' => $destinationAddress,
			'destinationDirection' => $destinationDirection,
			'itemName' => $itemName,
			'contents' => $contents,
			'useInsurance' => $useInsurance,
			'packageType' => $packageType,
			'externalId' => $externalId,
			'paymentType' => $paymentType,
			'cod' => $cod,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/orders/domestics',
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query($options),
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function internationalOrder(
		$origin
		,$destination
		,$weight
		,$length
		,$width
		,$height
		,$value
		,$rateId
		,$consignerName
		,$consignerPhoneNumber
		,$originAddress
		,$originDirection
		,$consigneeName
		,$consigneePhoneNumber
		,$destinationAddress
		,$destinationDirection
		,$destinationArea = ''
		,$destinationSuburb = ''
		,$destinationCity = ''
		,$destinationProvince = ''
		,$destinationPostCode = ''
		,$itemName
		,$contents
		,$useInsurance
		,$packageType
		,$externalId
		,$paymentType
		,$cod
	) {
		$method = 'POST';
		$options = [
			'apiKey' => $this->key,
			'o' => $origin,
			'd' => $destination,
			'wt' => $weight,
			'l' => $length,
			'w' => $width,
			'h' => $height,
			'v' => $value,
			'consignerName' => $consignerName,
			'consignerPhoneNumber' => $consignerPhoneNumber,
			'originAddress' => $originAddress,
			'originDirection' => $originDirection,
			'consigneeName' => $consigneeName,
			'consigneePhoneNumber' => $consigneePhoneNumber,
			'destinationAddress' => $destinationAddress,
			'destinationDirection' => $destinationDirection,
			'destinationArea' => $destinationArea,
			'destinationSuburb' => $destinationSuburb,
			'destinationProvince' => $destinationProvince,
			'destinationPostCode' => $destinationPostCode,
			'itemName' => $itemName,
			'contents' => $contents,
			'useInsurance' => $useInsurance,
			'packageType' => $packageType,
			'externalId' => $externalId,
			'paymentType' => $paymentType,
			'cod' => $cod,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/orders/internationals',
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query($options),
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getOrder($orderId) {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
			'id' => $orderId,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/orders?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function orderActivation($orderId, $active) {
		$method = 'PUT';
		$options = [
			'apiKey' => $this->key,
			'orderID' => $orderId,
			'active' => $active,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/activations?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query($options),
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getOrderDetail($orderId, $labelChecksum, $externalId) {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
			'orderId' => $orderId,
			'labelChecksum' => 'https://shipper.id/label/sticker.php?oid=' . $orderId . '&uid=' . $labelChecksum,
			'externalId' => $externalId,
		];

		if ($this->environment == 'dev' || $this->environment == 'development') {
			$options['labelChecksum'] = 'https://shipper.id/label-dev/sticker.php?oid=' . $orderId . '&uid=' . $labelChecksum;
		}

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/orders?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function generateAWB($externalId, $orderId) {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
			'eid' => $externalId,
			'oid' => $orderId,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/awbs/generate?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getAWB($externalId, $orderId) {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
			'eid' => $externalId,
			'oid' => $orderId,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/awbs?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function updateAWB($orderId, $awbNumber) {
		$method = 'PUT';
		$options = [
			'apiKey' => $this->key,
			'orderID' => $orderId,
			'awbNumber' => $awbNumber,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/awbs?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query($options),
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function updateOrder($orderId, $weight, $length, $height, $width) {
		$method = 'PUT';
		$options = [
			'apiKey' => $this->key,
			'orderID' => $orderId,
			'weight' => $weight,
			'length' => $length,
			'height' => $height,
			'width' => $width,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/orders?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query($options),
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function cancelOrder($orderId) {
		$method = 'PUT';
		$options = [
			'apiKey' => $this->key,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/orders/' . $orderId . '/cancel/?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function updateSubscription($orderId, $customAWB, $autoTrack) {
		$method = 'PUT';
		$options = [
			'apiKey' => $this->key,
			'customAWB' => $customAWB,
			'autoTrack' => $autoTrack,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/subscriptions/' . $orderId . '?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query($options),
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getOrderHistory($merchantId = '', $phone = '', $limit = '', $startDate = '', $endDate = '', $page = '') {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
			'merchantID' => $merchantID,
			'phone' => $phone,
			'limit' => $limit,
			'startDate' => $startDate,
			'endDate' => $endDate,
			'page' => $page,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/histories/orders?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function getLogistics($cityId) {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/logistics/' . $cityId . '?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	public function track() {
		$method = 'GET';
		$options = [
			'apiKey' => $this->key,
		];

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $this->url . 'public/v1/logistics/status?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($this->curl);
		$err = curl_error($this->curl);

		if ($err) {
			return $this->error($err);
		} else {
			return $this->response($response);
		}
	}

	private function response($res) {
		return json_decode($res);
	}

	private function error($err) {
		return (Object) [
			'status' => 'fail',
			'data' => $err,
		];
	}
}