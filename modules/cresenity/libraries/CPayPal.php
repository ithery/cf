<?php

# PayPal Library
//register autoload for paypal libraries
spl_autoload_register(array('CPayPal', 'loader'));

class CPayPal {

    private $client_id;
    private $client_secret;
    private $api_context;
    private $mode;
    private $items;
    private $details;

    public static function loader($class) {
        $arr_class = explode('_', $class);
        if (count($arr_class) > 0) {
            if ($arr_class[0] != 'PayPal')
                return false;
        }

        $paypal_path = dirname(__FILE__) . DS . 'Lib' . DS . 'PayPal';
        for ($i = 1; $i < count($arr_class); $i++) {
            $c = $arr_class[$i];
            $paypal_path.=DS . $c;
        }
        $paypal_path.=EXT;

        if (file_exists($paypal_path)) {
            require_once $paypal_path;
            return true;
        }
        return false;
    }

    protected function __construct($options = array()) {
        $this->client_id = carr::get($options, 'client_id');
        $this->client_secret = carr::get($options, 'client_secret');
        $this->mode = carr::get($options, 'mode', 'live');


        $this->api_context = new PayPal_Rest_ApiContext(
                new PayPal_Auth_OAuthTokenCredential(
                $this->client_id, $this->client_secret
                )
        );

        $this->api_context->setConfig(
                array(
                    'mode' => $this->mode,
                    'log.LogEnabled' => true,
                    'log.FileName' => '../PayPal.log',
                    'log.LogLevel' => 'DEBUG', // PLEASE USE `FINE` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                    'cache.enabled' => true,
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                )
        );
        $this->items = array();
    }

    public function payment($data) {
//        $data = array(
//            'items' => array(
//                array(
//                    'name' => 'Ground Coffee 40 oz',
//                    'currency' => 'USD',
//                    'qty' => '1',
//                    'sku' => '123456',
//                    'price' => '7.5',
//                ),
//                array(
//                    'name' => 'Granola Bars',
//                    'currency' => 'USD',
//                    'qty' => '5',
//                    'sku' => '123123',
//                    'price' => '2',
//                ),
//            ),
//            'currency' => 'USD',
//            'description' => 'Payment Description',
//            'invoice_number' => uniqid(),
//            'return_url' => curl::httpbase() . 'paypal/success',
//            'cancel_url' => curl::httpbase() . 'paypal/cancel',
//            'details' => array(
//                'shipping' => '1.2',
//                'tax' => '1.2',
//                'handling_fee' => null,
//                'shipping_discount' => null,
//                'insurance' => null,
//                'gift_wrap' => null,
//                'fee' => null,
//            ),
//        );

        $currency = carr::get($data, 'currency');
        $description = carr::get($data, 'description');
        $invoice_number = carr::get($data, 'invoice_number');
        $return_url = carr::get($data, 'return_url');
        $cancel_url = carr::get($data, 'cancel_url');

        $payer = new PayPal_Api_Payer();
        $payer->setPaymentMethod("paypal");

        $data_items = carr::get($data, 'items');
        $item_total = 0;
        foreach ($data_items as $item) {
            $name = carr::get($item, 'name');
            $qty = carr::get($item, 'qty');
            $sku = carr::get($item, 'sku');
            $price = carr::get($item, 'price');
            $currency = carr::get($item, 'currency');
            $item = new PayPal_Api_Item();
            $item->setName($name)
                    ->setCurrency($currency)
                    ->setQuantity($qty)
                    ->setSku($sku)
                    ->setPrice($price);
            $item_total+=$price;
            $this->items[] = $item;
        }

        $item_list = new PayPal_Api_ItemList();
        $item_list->setItems($this->items);

        $data_details = carr::get($data, 'details');
        $shipping = carr::get($data_details, 'shipping');
        $tax = carr::get($data_details, 'tax');


        $details = new PayPal_Api_Details();
        $details->setShipping($shipping)
                ->setTax($tax)
                ->setSubtotal($item_total);

        $total = $item_total + $shipping + $tax;

        if (strlen($invoice_number) == 0) {
            $invoice_number = uniqid();
        }

        $amount = new PayPal_Api_Amount();
        $amount->setCurrency($currency)
                ->setTotal($total)
                ->setDetails($details);

        $transaction = new PayPal_Api_Transaction();
        $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription($description)
                ->setInvoiceNumber($invoice_number);


        $redirectUrls = new PayPal_Api_RedirectUrls();
        $redirectUrls->setReturnUrl($return_url)->setCancelUrl($cancel_url);

        $payment = new PayPal_Api_Payment();
        $payment->setIntent("sale")
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions(array($transaction));

        try {
            $payment->create($this->api_context);
        } catch (PayPal_Exception_PayPalConnectionException $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        $approval_url = $payment->getApprovalLink();
        return $approval_url;
    }

    public function execute_payment($data) {
        $result = array();
        $error_code = 0;
        $error_message = "";

        $paymentId = $data['paymentId'];
        try {
            $payment = PayPal_Api_Payment::get($paymentId, $this->api_context);
        } catch (PayPal_Exception_PayPalConnectionException $ex) {
            throw new Exception("Error(1) Get Payment: " . $ex->getMessage(), $ex->getCode());
        } catch (Exception $ex) {
            throw new Exception("Error(1) Get Payment: " . $ex->getMessage());
        }
        
        $execution = new PayPal_Api_PaymentExecution();
        $execution->setPayerId($data['PayerID']);

        try {
            $result = $payment->execute($execution, $this->api_context);

            try {
                $payment = PayPal_Api_Payment::get($paymentId, $this->api_context);
            } catch (PayPal_Exception_PayPalConnectionException $ex) {
                throw new Exception("Error(2) Get Payment: " . $ex->getMessage(), $ex->getCode());
            } catch (Exception $ex) {
                throw new Exception("Error(2) Get Payment: " . $ex->getMessage());
            }
        } catch (PayPal_Exception_PayPalConnectionException $ex) {
            throw new Exception("Error Execute Payment: " . $ex->getMessage(), $ex->getCode());
        } catch (Exception $ex) {
            throw new Exception("Error Execute Payment: " . $ex->getMessage());
        }

        if ($error_code == 0) {
            if ($payment->getState() == "approved") {
                $result = array("err_code" => $error_code, "err_message" => "", "payment_id" => $payment->getId(), "payment_data" => $payment);
            } else {
                $result = array("err_code" => "003", "err_message" => "Status from Paypal: " . strtoupper($payment->getState()), "payment_id" => $payment->getId(), "payment_data" => $payment);
            }
        } else {
            $result = array("err_code" => $error_code, "err_message" => $error_message);
        }
        return $result;
    }

    public static function factory($options = array()) {
        return new CPayPal($options);
    }

}

?>
