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

		return "
			<html>
			<head>
			<title>senangPay Sample Code</title>
			</head>
			<body onload='document.order.submit()'>
			    <form name='order' method='post' action='" . $this->url . "'>
			        <input type='hidden' name='detail' value='" . $detail . "'>
			        <input type='hidden' name='amount' value='" . $amount . "'>
			        <input type='hidden' name='order_id' value='" . $orderId . "'>
			        <input type='hidden' name='name' value='" . $name . "'>
			        <input type='hidden' name='email' value='" . $email . "'>
			        <input type='hidden' name='phone' value='" . $phone . "'>
			        <input type='hidden' name='hash' value='" . $this->hashString($detail, $amount, $orderId) . "'>
			    </form>
			</body>
			</html>
		";
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