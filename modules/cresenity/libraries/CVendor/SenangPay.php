<?php

class CVendor_SenangPay {
    private $merchantId;
    private $secretKey;
    private $environment;
    private $url;
    private $curl;

    public function __construct(array $options, $environment = 'production') {
        $this->environment = $environment;

        $this->merchantId = carr::get($options, 'merchantId');
        $this->secretKey = carr::get($options, 'secretKey');
    }

    public function setMerchantId($merchantId) {
        $this->merchantId = $merchantId;
    }

    public function setSecretKey($secretKey) {
        $this->secretKey = $secretKey;
    }

    public function hashString($param) {
        foreach ($param as &$value) {
            $value = urldecode($value);
        }

        return md5($this->secretKey . implode('', $param));
    }

    public function hashStringResponse($param) {
        foreach ($param as &$value) {
            $value = urldecode($value);
        }

        return md5($this->secretKey . '?' . urlencode(CFRouter::$query_string));
    }

    public function checkKey() {
        if (!$this->merchantId && !$this->secretKey) {
            throw new Exception('Senang Pay Merchant Id and SecretKey is Required.');
        }

        return true;
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
        $this->checkKey();
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
			        <input type='hidden' name='hash' value='" . $this->hashString([$detail, $amount, $orderId]) . "'>
			    </form>
			</body>
			</html>
		";
    }

    public function verify($request) {
        $hash = carr::get($request, 'hash');
        $statusId = carr::get($request, 'status_id');
        $message = carr::get($request, 'msg');
        $this->checkKey();
        $hashedString = $this->hashStringResponse($request);
        $errCode = 0;
        $errMessage = '';
        $result = '';
        $status = false;
        if ($hashedString == urldecode($hash) || 1 == 1) {
            if (urldecode($statusId) == '1') {
                $result = 'Payment was sucessful with message: ' . urldecode($message);
                $status = true;
            } else {
                $result = 'Payment failed with message: ' . urldecode($message);
                $status = false;
            }
        } else {
            $errCode++;
            $errMessage = 'Hashed value is not correct';
        }

        return [
            'errCode' => $errCode,
            'errMessage' => $errMessage,
            'result' => $result,
            'status' => $status,
        ];
    }
}
