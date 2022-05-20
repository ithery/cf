<?php

/**
 * API methods to get transaction status, approve and cancel transactions.
 */
class CVendor_Midtrans_Transaction {
    /**
     * Retrieve transaction status.
     *
     * @param string $id Order ID or transaction ID
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function status($id) {
        return CVendor_Midtrans_ApiRequestor::get(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/' . $id . '/status',
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }

    /**
     * Retrieve B2B transaction status.
     *
     * @param string $id Order ID or transaction ID
     *
     * @throws Exception
     *
     * @return mixed[]
     */
    public static function statusB2b($id) {
        return CVendor_Midtrans_ApiRequestor::get(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/' . $id . '/status/b2b',
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }

    /**
     * Approve challenge transaction.
     *
     * @param string $id Order ID or transaction ID
     *
     * @throws Exception
     *
     * @return string
     */
    public static function approve($id) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/' . $id . '/approve',
            CVendor_Midtrans_Config::$serverKey,
            false
        )->status_code;
    }

    /**
     * Cancel transaction before it's settled.
     *
     * @param string $id Order ID or transaction ID
     *
     * @throws Exception
     *
     * @return string
     */
    public static function cancel($id) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/' . $id . '/cancel',
            CVendor_Midtrans_Config::$serverKey,
            false
        )->status_code;
    }

    /**
     * Expire transaction before it's setteled.
     *
     * @param string $id Order ID or transaction ID
     *
     * @throws Exception
     *
     * @return mixed[]
     */
    public static function expire($id) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/' . $id . '/expire',
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }

    /**
     * Transaction status can be updated into refund
     * if the customer decides to cancel completed/settlement payment.
     * The same refund id cannot be reused again.
     *
     * @param string $id Order ID or transaction ID
     * @param $params
     *
     * @throws Exception
     *
     * @return mixed[]
     */
    public static function refund($id, $params) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/' . $id . '/refund',
            CVendor_Midtrans_Config::$serverKey,
            $params
        );
    }

    /**
     * Transaction status can be updated into refund
     * if the customer decides to cancel completed/settlement payment.
     * The same refund id cannot be reused again.
     *
     * @param string $id     Order ID or transaction ID
     * @param mixed  $params
     *
     * @throws Exception
     *
     * @return mixed[]
     */
    public static function refundDirect($id, $params) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/' . $id . '/refund/online/direct',
            CVendor_Midtrans_Config::$serverKey,
            $params
        );
    }

    /**
     * Deny method can be triggered to immediately deny card payment transaction
     * in which fraud_status is challenge.
     *
     * @param string $id Order ID or transaction ID
     *
     * @throws Exception
     *
     * @return mixed[]
     */
    public static function deny($id) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/' . $id . '/deny',
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }
}
