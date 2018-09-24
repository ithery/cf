<?php

class CVendor_SenangPay {
	
	private $merchantId = '';
	private $secretKey = '';
	private $curl;

	public function __construct() {
		
	}

	public function setMerchantId($merchantId) {
		$this->merchantId = $merchantId;
	}

	public function setSecretKey($secretKey) {
		$this->secretKey = $secretKey;
	}

	public function hashString(...$param) {
		return md5($this->secretKey . implode('', urldecode($param)));
	}

	public function payment(
		$detail,
		$amount,
		$orderId,
		$name,
		$email,
		$phone
	) {
		$curl = CCurl::factory('https://app.senangpay.my/payment/' . $this->merchantId);

		$data = [
			'detail' => $detail,
			'amount' => $amount,
			'order_id' => $orderId,
			'name' => $name,
			'email' => $email,
			'phone' => $phone,
			'hash' => $this->hashString($detail, $amount, $orderId),
		];

		$curl->setPost($data);
		$curl->exec();

		return $curl->response();
	}

	public function verify(
		$statusId,
		$orderId,
		$message,
		$transactionId,
		$hash
	) {
		$hashedString = $this->hashString($statusId, $orderId, $transactionId, $message);

		if ($hashedString == urldecode($hash)) {
			if (urldecode($statusId) == '1') {
				return 'Payment was sucessful with message: ' . urldecode($message);
			} else {
				return 'Payment failed with message: ' . urldecode($message);
			}
		} else {
			return 'Hashed value is not correct';
		}
	}

}