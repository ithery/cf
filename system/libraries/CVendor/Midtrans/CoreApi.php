<?php

/**
 * Provide charge and capture functions for Core API.
 */
class CVendor_Midtrans_CoreApi {
    /**
     * Create transaction.
     *
     * @param mixed[] $params Transaction options
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function charge($params) {
        $payloads = [
            'payment_type' => 'credit_card'
        ];

        if (isset($params['item_details'])) {
            $gross_amount = 0;
            foreach ($params['item_details'] as $item) {
                $gross_amount += $item['quantity'] * $item['price'];
            }
            $payloads['transaction_details']['gross_amount'] = $gross_amount;
        }

        $payloads = array_replace_recursive($payloads, $params);

        if (CVendor_Midtrans_Config::$isSanitized) {
            CVendor_Midtrans_Sanitizer::jsonRequest($payloads);
        }

        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/charge',
            CVendor_Midtrans_Config::$serverKey,
            $payloads
        );
    }

    /**
     * Capture pre-authorized transaction.
     *
     * @param string $param Order ID or transaction ID, that you want to capture
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function capture($param) {
        $payloads = [
            'transaction_id' => $param,
        ];

        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/capture',
            CVendor_Midtrans_Config::$serverKey,
            $payloads
        );
    }

    /**
     * Do `/v2/card/register` API request to Core API.
     *
     * @param $cardNumber
     * @param $expMoth
     * @param $expYear
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function cardRegister($cardNumber, $expMoth, $expYear) {
        $path = '/card/register?card_number=' . $cardNumber
            . '&card_exp_month=' . $expMoth
            . '&card_exp_year=' . $expYear
            . '&client_key=' . CVendor_Midtrans_Config::$clientKey;

        return CVendor_Midtrans_ApiRequestor::get(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2' . $path,
            CVendor_Midtrans_Config::$clientKey,
            false
        );
    }

    /**
     * Do `/v2/token` API request to Core API.
     *
     * @param $cardNumber
     * @param $expMoth
     * @param $expYear
     * @param $cvv
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function cardToken($cardNumber, $expMoth, $expYear, $cvv) {
        $path = '/token?card_number=' . $cardNumber
            . '&card_exp_month=' . $expMoth
            . '&card_exp_year=' . $expYear
            . '&card_cvv=' . $cvv
            . '&client_key=' . CVendor_Midtrans_Config::$clientKey;

        return CVendor_Midtrans_ApiRequestor::get(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2' . $path,
            CVendor_Midtrans_Config::$clientKey,
            false
        );
    }

    /**
     * Do `/v2/point_inquiry/<tokenId>` API request to Core API.
     *
     * @param string $tokenId tokenId of credit card (more params detail refer to: https://api-docs.midtrans.com)
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function cardPointInquiry($tokenId) {
        return CVendor_Midtrans_ApiRequestor::get(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/point_inquiry/' . $tokenId,
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }

    /**
     * Create `/v2/pay/account` API request to Core API.
     *
     * @param string $param create pay account request (more params detail refer to: https://api-docs.midtrans.com/#create-pay-account)
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function linkPaymentAccount($param) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/pay/account',
            CVendor_Midtrans_Config::$serverKey,
            $param
        );
    }

    /**
     * Do `/v2/pay/account/<accountId>` API request to Core API.
     *
     * @param string $accountId accountId (more params detail refer to: https://api-docs.midtrans.com/#get-pay-account)
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function getPaymentAccount($accountId) {
        return CVendor_Midtrans_ApiRequestor::get(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/pay/account/' . $accountId,
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }

    /**
     * Unbind `/v2/pay/account/<accountId>/unbind` API request to Core API.
     *
     * @param string $accountId accountId (more params detail refer to: https://api-docs.midtrans.com/#unbind-pay-account)
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function unlinkPaymentAccount($accountId) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v2/pay/account/' . $accountId . '/unbind',
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }

    /**
     * Create `/v1/subscription` API request to Core API.
     *
     * @param string $param create subscription request (more params detail refer to: https://api-docs.midtrans.com/#create-subscription)
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function createSubscription($param) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v1/subscriptions',
            CVendor_Midtrans_Config::$serverKey,
            $param
        );
    }

    /**
     * Do `/v1/subscription/<subscription_id>` API request to Core API.
     *
     * @param string $SubscriptionId get subscription request (more params detail refer to: https://api-docs.midtrans.com/#get-subscription)
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function getSubscription($SubscriptionId) {
        return CVendor_Midtrans_ApiRequestor::get(
            CVendor_Midtrans_Config::getBaseUrl() . '/v1/subscriptions/' . $SubscriptionId,
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }

    /**
     * Do disable `/v1/subscription/<subscription_id>/disable` API request to Core API.
     *
     * @param string $SubscriptionId disable subscription request (more params detail refer to: https://api-docs.midtrans.com/#disable-subscription)
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function disableSubscription($SubscriptionId) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v1/subscriptions/' . $SubscriptionId . '/disable',
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }

    /**
     * Do enable `/v1/subscription/<subscription_id>/enable` API request to Core API.
     *
     * @param string $SubscriptionId enable subscription request (more params detail refer to: https://api-docs.midtrans.com/#enable-subscription)
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function enableSubscription($SubscriptionId) {
        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getBaseUrl() . '/v1/subscriptions/' . $SubscriptionId . '/enable',
            CVendor_Midtrans_Config::$serverKey,
            false
        );
    }

    /**
     * Do update subscription `/v1/subscription/<subscription_id>` API request to Core API.
     *
     * @param string $SubscriptionId update subscription request (more params detail refer to: https://api-docs.midtrans.com/#update-subscription)
     * @param string $param
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function updateSubscription($SubscriptionId, $param) {
        return CVendor_Midtrans_ApiRequestor::patch(
            CVendor_Midtrans_Config::getBaseUrl() . '/v1/subscriptions/' . $SubscriptionId,
            CVendor_Midtrans_Config::$serverKey,
            $param
        );
    }
}
