<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 17, 2019, 7:55:48 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CVendor_Xendit {

    public function __construct($options) {
        $this->server_domain = 'https://api.xendit.co';
        $this->secret_api_key = $options['secret_api_key'];
    }

    public function createInvoice($external_id, $amount, $payer_email, $description, $invoice_options = null) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/v2/invoices';
        $data['external_id'] = $external_id;
        $data['amount'] = (int) $amount;
        $data['payer_email'] = $payer_email;
        $data['description'] = $description;
        if (!empty($invoice_options['callback_virtual_account_id'])) {
            $data['callback_virtual_account_id'] = $invoice_options['callback_virtual_account_id'];
        }
        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function createDisbursement($external_id, $amount, $bank_code, $account_holder_name, $account_number, $disbursement_options = null) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        if (!empty($disbursement_options['X-IDEMPOTENCY-KEY'])) {
            array_push($headers, 'X-IDEMPOTENCY-KEY: ' . $disbursement_options['X-IDEMPOTENCY-KEY']);
        }
        $end_point = $this->server_domain . '/disbursements';
        $data['external_id'] = $external_id;
        $data['amount'] = (int) $amount;
        $data['bank_code'] = $bank_code;
        $data['account_holder_name'] = $account_holder_name;
        $data['account_number'] = $account_number;
        if (!empty($disbursement_options['description'])) {
            $data['description'] = $disbursement_options['description'];
        }

        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function getVirtualAccountBanks() {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/available_virtual_account_banks';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function createCallbackVirtualAccount($external_id, $bank_code, $name, $virtual_account_number = null, $options = array()) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/callback_virtual_accounts';
        $data['external_id'] = $external_id;
        $data['bank_code'] = $bank_code;
        $data['name'] = $name;
        $isSingleUse = carr::get($options, 'is_single_use', null);
        $isClosed = carr::get($options, 'is_closed', null);
        $suggestedAmount = carr::get($options, 'suggested_amount', null);
        $expectedAmount = carr::get($options, 'expected_amount', null);
        $expirationDate = carr::get($options, 'expiration_date', null);
        $description = carr::get($options, 'description', null);
        if (!empty($virtual_account_number)) {
            $data['virtual_account_number'] = $virtual_account_number;
        }
        if (!empty($isSingleUse)) {
            $data['is_single_use'] = $isSingleUse;
        }
        if (!empty($isClosed)) {
            $data['is_closed'] = $isClosed;
        }
        if (!empty($suggestedAmount)) {
            $data['suggested_amount'] = $suggestedAmount;
        }
        if (!empty($expectedAmount)) {
            $data['expected_amount'] = $expectedAmount;
        }
        if (!empty($expirationDate)) {
            $data['expiration_date'] = $expirationDate;
        }
        if (!empty($description)) {
            $data['description'] = $expirationDate;
        }
        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function updateCallbackVirtualAccount($virtualAccountId, $options = array()) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/callback_virtual_accounts/' . $virtualAccountId;
        $isSingleUse = carr::get($options, 'is_single_use', null);
        $suggestedAmount = carr::get($options, 'suggested_amount', null);
        $expectedAmount = carr::get($options, 'expected_amount', null);
        $expirationDate = carr::get($options, 'expiration_date', null);
        $description = carr::get($options, 'description', null);
        $data = array();
        if (!empty($isSingleUse)) {
            $data['is_single_use'] = $isSingleUse;
        }
        if (!empty($suggestedAmount)) {
            $data['suggested_amount'] = $suggestedAmount;
        }
        if (!empty($expectedAmount)) {
            $data['expected_amount'] = $expectedAmount;
        }
        if (!empty($expirationDate)) {
            $data['expiration_date'] = $expirationDate;
        }
        if (!empty($description)) {
            $data['description'] = $expirationDate;
        }
        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function getCallbackVirtualAccount($virtualAccountId) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/callback_virtual_accounts/' . $virtualAccountId;

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function getDisbursement($disbursement_id) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/disbursements/' . $disbursement_id;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function getAvailableDisbursementBanks() {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/available_disbursements_banks';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function getInvoice($invoice_id) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/v2/invoices/' . $invoice_id;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function getBalance() {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/balance';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function captureCreditCardPayment($external_id, $token_id, $amount, $capture_options = null) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/credit_card_charges';
        $data['external_id'] = $external_id;
        $data['token_id'] = $token_id;
        $data['amount'] = $amount;
        if (!empty($capture_options['authentication_id'])) {
            $data['authentication_id'] = $capture_options['authentication_id'];
        }
        if (!empty($capture_options['card_cvn'])) {
            $data['card_cvn'] = $capture_options['card_cvn'];
        }
        if (!empty($capture_options['capture'])) {
            $data['capture'] = $capture_options['capture'];
        }
        if (!empty($capture_options['interval'])) {
            $data['interval'] = $capture_options['interval'];
        }
        if (!empty($capture_options['interval_count'])) {
            $data['interval_count'] = $capture_options['interval_count'];
        }
        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function issueCreditCardRefund($credit_card_charge_id, $amount, $external_id, $options = null) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        if (!empty($options['X-IDEMPOTENCY-KEY'])) {
            array_push($headers, 'X-IDEMPOTENCY-KEY: ' . $options['X-IDEMPOTENCY-KEY']);
        }
        $end_point = $this->server_domain . '/credit_card_charges/' . $credit_card_charge_id . '/refunds';
        $data['amount'] = $amount;
        $data['external_id'] = $external_id;
        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->public_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function validateBankAccountHolderName($bank_account_number, $bank_code) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/bank_account_data_requests';
        $data['bank_account_number'] = $bank_account_number;
        $data['bank_code'] = $bank_code;
        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function pauseRecurringPayment($idXendit = null) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/recurring_payments/' . $idXendit . '/pause!';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($curl);

        $info = curl_getinfo($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function resumeRecurringPayment($idXendit = null) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/recurring_payments/' . $idXendit . '/resume!';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($curl);

        $info = curl_getinfo($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function getRecurringPayment($id = null) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/recurring_payments/' . $id;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($curl);

        $info = curl_getinfo($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }
    
    public function stopRecurringPayment($idXendit = null) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/recurring_payments/' . $idXendit . '/stop!';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($curl);

        $info = curl_getinfo($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function createRecurringPayment($externalId, $payerEmail, $interval, $intervalCount, $description, $amount, $options = array()) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/recurring_payments';

        $data = array();
        //string ID of your choice (typically the unique identifier of a recurring payment in your system)
        $data['external_id'] = $externalId;
        //string Email of the end user you're charging
        $data['payer_email'] = $payerEmail;
        //string One of DAY, WEEK, MONTH. The frequency with which a recurring payment invoice should be billed.
        $data['interval'] = $interval;
        //number The number of intervals (specified in the interval property) between recurring. For example, interval=MONTH and interval_count=3 bills every 3 months.
        $data['interval_count'] = $intervalCount;
        //string Description for the recurring payment and invoices
        $data['description'] = $description;
        //number Amount per invoice per interval.
        //The minimum amount to create an invoice is 10.000 IDR. The maximum amount is 1.000.000.000 IDR
        $data['amount'] = $amount;
        if (!empty($options['invoice_duration'])) {
            //number duration of time that end user have in order to pay the invoice before it's expired (in Second). If it's not filled, invoice_duration will follow your business default invoice duration. 
            //invoice_duration should and will always be less than the interval-interval_count combination.
            $data['invoice_duration'] = $options['invoice_duration'];
        }
        if (!empty($options['should_send_email'])) {
            //boolean Specify should the end user get email when invoice is created, paid, or expired; or not
            $data['should_send_email'] = $options['should_send_email'];
        }
        if (!empty($options['missed_payment_action'])) {
            //string One of IGNORE, STOP. If there is an invoice from a recurring payment that expired, IGNORE will continue with the recurring payment as usual. STOP will stop the recurring payment.
            $data['missed_payment_action'] = $options['missed_payment_action'];
        }
        if (!empty($options['credit_card_token'])) {
            //string Token ID for credit card autocharge. If it's empty then the autocharge is disabled. This token must be multiple use (is_multiple_use is true). please refer create cards token on how to create multi-use token. The token will still be there even if it's failed to charge
            $data['credit_card_token'] = $options['credit_card_token'];
        }
        if (!empty($options['start_date'])) {
            //string (ISO 8601) time when the first invoice will be issued. When left blank, the invoice will be created immediately
            $data['start_date'] = $options['start_date'];
        }
        if (!empty($options['success_redirect_url'])) {
            //string url that end user will be redirected to upon successful payment to invoice created by this recurring payment. 
            //example : https://yourcompany.com/example_item/10/success_page
            $data['success_redirect_url'] = $options['success_redirect_url'];
        }
        if (!empty($options['failure_redirect_url'])) {
            //string url that end user will be redirected to upon expireation of invoice created by this recurring payment. 
            //example : https://yourcompany.com/example_item/10/failed_checkout
            $data['failure_redirect_url'] = $options['failure_redirect_url'];
        }

        if (carr::get($options, 'charge_immediately') == false) {
            //string url that end user will be redirected to upon expireation of invoice created by this recurring payment.
            //example : https://yourcompany.com/example_item/10/failed_checkout
            $data['charge_immediately'] = $options['charge_immediately'];
        }


        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function createEWallet($external_id, $ewallet_type, $phone, $amount, $options = array()) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/ewallets';
        $data['external_id'] = $external_id;
        $data['ewallet_type'] = $ewallet_type;
        $data['phone'] = $phone;
        $data['amount'] = $amount;
        if ($ewallet_type == 'LINKAJA' || $ewallet_type == 'DANA') {
            $data['items'][] = carr::get($options, 'items');
            $data['callback_url'] = carr::get($options, 'callbackUrl');
            $data['redirect_url'] = carr::get($options, 'redirectUrl');
        }


        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function createRetail($external_id, $retail_outlet_name, $name, $amount, $options = array()) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/fixed_payment_code';
        $data['external_id'] = $external_id;
        $data['retail_outlet_name'] = $retail_outlet_name;
        $data['name'] = $name;
        $data['expected_amount'] = $amount;
        $isSingleUse = carr::get($options, 'is_single_use', null);
        $expirationDate = carr::get($options, 'expiration_date', null);
        if (!empty($isSingleUse)) {
            $data['is_single_use'] = $isSingleUse;
        }
        if (!empty($expirationDate)) {
            $data['expiration_date'] = $expirationDate;
        }
        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function cardlessCredit($externalId, $cardlessCreditType = null, $amount, $paymentType = '', $options = array()) {
        $curl = curl_init();
        $headers = array();
        $data = array();

        $item = new stdClass();
        $item->id = "123123";
        $item->name = "Phone Case";
        $item->price = 200000;
        $item->type = "Smartphone";
        $item->url = "http://example.com/phone/phone_case";
        $item->quantity = 2;

        $headers[] = 'Content-Type: application/json';
        $end_point = $this->server_domain . '/cardless-credit';
        $data['external_id'] = $externalId;
        if (strlen($cardlessCreditType) > 0) {
            $data['cardless_credit_type'] = $cardlessCreditType;
        }
        if (strlen($paymentType) > 0) {
            $data['payment_type'] = $paymentType;
        }
        if (strlen($amount) > 0) {
            $data['amount'] = $amount;
        }
        if (is_array($options)) {
            $data['items'][] = $item;
            $data['callback_url'] = carr::get($options, 'callbackUrl');
            $data['redirect_url'] = carr::get($options, 'redirectUrl');
            $data['customer_details'] = carr::get($options, 'customerDetails');
            $data['shipping_address'] = carr::get($options, 'shippingAddress');
            $data['redirect_url'] = 'https://example.com';
            $data['callback_url'] = 'http://example.com/callback-cardless-credit';
        }


        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

    public function invoices($data = null) {
        $endPoint = $this->server_domain . '/v2/invoices';

        $response = $this->requestToXendit($endPoint, 'GET', $data);
        return $response;
    }

    public function balance($accountType = 'CASH') {
        $endPoint = $this->server_domain . '/balance?account_type=' . $accountType;
        $response = $this->requestToXendit($endPoint, 'GET');
        return $response;
    }

    public function recurringPayments($id) {
        $endPoint = $this->server_domain . '/recurring_payments/' . $id;
        $response = $this->requestToXendit($endPoint, 'GET');
        return $response;
    }

    public function virtualAccountSimulatePayment($id, $amount) {
        $endPoint = $this->server_domain . '/callback_virtual_accounts/external_id=' . $id . '/simulate_payment';
        $response = $this->requestToXendit($endPoint, 'POST', ['amount' => $amount]);
        return $response;
    }

    public function retailSimulatePayment($external_id, $retail_outlet_name, $payment_code, $transfer_amount) {
        $endPoint = $this->server_domain . '/fixed_payment_code/simulate_payment';
        $data = [];
        $data['external_id'] = $external_id;
        $data['retail_outlet_name'] = $retail_outlet_name;
        $data['payment_code'] = $payment_code;
        $data['transfer_amount'] = $transfer_amount;
        $response = $this->requestToXendit($endPoint, 'POST', $data);
        return $response;
    }

    protected function requestToXendit($endPoint, $method, $data = null) {
        $curl = curl_init();
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_api_key . ":");
        curl_setopt($curl, CURLOPT_URL, $endPoint);
        if ($method == 'POST') {
            if ($data != null) {
                $payload = json_encode($data);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
            }
        } else {
            if ($data != null) {
                $payload = curl::as_post_string($data);
                curl_setopt($curl, CURLOPT_URL, $endPoint . '?' . $payload);
            }
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        curl_close($curl);
        $responseObject = json_decode($response, true);
        return $responseObject;
    }

}
