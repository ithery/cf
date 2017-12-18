<?php

namespace RajaOngkir;

class RajaOngkir {
	private $key = '9406fbe823f72dbfbe4d9b3c849c073e';
	private $android_key;
	private $ios_key;

	function setKey($key) {
		$this->key = $key;
	}

	function setAndroidKey($key) {
		$this->android_key = $key;
	}

	function setIosKey($key) {
		$this->ios_key = $key;
	}

	function getProvince($provinceID = '') {
		$curl = curl_init();
		$method = 'GET';
		$options = [
			'id' => $provinceID,
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://pro.rajaongkir.com/api/province?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => [
				'key: ' . $this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}

	function getCity($provinceID = '', $cityID = '') {
		$curl = curl_init();
		$method = 'GET';
		$options = [
			'id' => $cityID,
			'province' => $provinceID,
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://pro.rajaongkir.com/api/city?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => [
				'key: ' . $this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}

	function getDistrict($cityID = '', $districtID = '') {
		$curl = curl_init();
		$method = 'GET';
		$options = [
			'id' => $districtID,
			'city' => $cityID,
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://pro.rajaongkir.com/api/subdistrict?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => [
				'key: ' . $this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}

	function getCost(
		$originType,
		$origin,
		$destinationType,
		$destination,
		$weight,
		$courier,
		$length = '',
		$width = '',
		$height = '',
		$diameter = ''
	) {
		$curl = curl_init();
		$method = 'POST';
		$options = [
			'origin' => $origin,
			'originType' => $originType,
			'destination' => $destination,
			'destinationType' => $destinationType,
			'weight' => $weight,
			'courier' => $courier,
			'length' => $length,
			'width' => $width,
			'height' => $height,
			'diameter' => $diameter,
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://pro.rajaongkir.com/api/cost',
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query($options),
			CURLOPT_HTTPHEADER => [
				'content-type: application/x-www-form-urlencoded',
				'key: ' . $this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}

	function getInternationalOrigin($provinceID = '', $cityID = '') {
		$curl = curl_init();
		$method = 'GET';
		$options = [
			'id' => $cityID,
			'province' => $provinceID,
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://pro.rajaongkir.com/api/v2/internationalOrigin?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => [
				'key: ' . $this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}

	function getInternationalDestination($countryID = '') {
		$curl = curl_init();
		$method = 'GET';
		$options = [
			'id' => $countryID,
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://pro.rajaongkir.com/api/v2/internationalDestination?' . http_build_query($options),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => [
				'key: ' . $this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}

	function getInternationalCost(
		$origin,
		$destination,
		$weight,
		$courier,
		$length = '',
		$width = '',
		$height = ''
	) {
		$curl = curl_init();
		$method = 'POST';
		$options = [
			'origin' => $origin,
			'destination' => $destination,
			'weight' => $weight,
			'courier' => $courier,
			'length' => $length,
			'width' => $width,
			'height' => $height,
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://pro.rajaongkir.com/api/v2/internationalCost',
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query($options),
			CURLOPT_HTTPHEADER => [
				'content-type: application/x-www-form-urlencoded',
				'key: ' . $this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}

	function getCurrency() {
		$curl = curl_init();
		$method = 'GET';

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://pro.rajaongkir.com/api/currency',
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => [
				'key: ' . $this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}

	function getWaybill($courier, $waybill) {
		$curl = curl_init();
		$method = 'POST';
		$options = [
			'waybill' => $waybill,
			'courier' => $courier,
		];

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://pro.rajaongkir.com/api/waybill',
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => http_build_query($options),
			CURLOPT_HTTPHEADER => [
				'key: ' . $this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}
}