<?php

class CVendor_SenangPay {
	
	private $merchantId = '509153777899632';
	private $secretKey = '74-939';
	private $environment;
	private $url;
	private $curl;

	public function __construct($environment = 'production') {
		$this->environment = $environment;
	}

	public function setMerchantId($merchantId) {
		$this->merchantId = $merchantId;
	}

	public function setSecretKey($secretKey) {
		$this->secretKey = $secretKey;
	}

	public function hashString(...$param) {
		foreach ($param as &$value) {
			$value = urldecode($value);
		}

		return md5($this->secretKey . implode('', $param));
	}

	public function createUrl() {
		$this->url = 'https://app.senangpay.my/payment/' . $this->merchantId;
		if ($this->environment == 'dev' || $this->environment == 'development') {
			$this->url = 'https://sandbox.senangpay.my/payment/' . $this->merchantId;
		}
	}

	public function payment(
		$detail,
		$amount,
		$orderId,
		$name,
		$email,
		$phone
	) {
		$this->createUrl();
		$this->curl = CCurl::factory($this->url);

		$data = [
			'detail' => $detail,
			'amount' => $amount,
			'order_id' => $orderId,
			'name' => $name,
			'email' => $email,
			'phone' => $phone,
			'hash' => $this->hashString($detail, $amount, $orderId),
		];

		// $this->curl->setPost($data);
		$this->curl->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
		$this->curl->setOpt(CURLOPT_POSTFIELDS, http_build_query($data));
		$this->curl->exec();

		return $this->curl->response();
	}

	public function verify(
		$statusId,
		$orderId,
		$message,
		$transactionId,
		$hash
	) {
		$hashedString = $this->hashString($statusId, $orderId, $transactionId, $message);
		$errCode = 0;
		$errMessage = '';
		$result = '';

		if ($hashedString == urldecode($hash)) {
			if (urldecode($statusId) == '1') {
				$result = 'Payment was sucessful with message: ' . urldecode($message);
			} else {
				$errCode++;
				$errMessage = 'Payment failed with message: ' . urldecode($message);
			}
		} else {
			$errCode++;
			$errMessage = 'Hashed value is not correct';
		}

		return [
			'errCode' => $errCode,
			'errMessage' => $errMessage,
			'result' => $result,
		];
	}

}