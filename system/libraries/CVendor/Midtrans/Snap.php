<?php
/**
 * Create Snap payment page and return snap token.
 */
class CVendor_Midtrans_Snap {
    /**
     * Create Snap payment page.
     *
     * Example:
     *
     * ```php
     *
     *   namespace Midtrans;
     *
     *   $params = array(
     *     'transaction_details' => array(
     *       'order_id' => rand(),
     *       'gross_amount' => 10000,
     *     )
     *   );
     *   $paymentUrl = Snap::getSnapToken($params);
     * ```
     *
     * @param array $params Payment options
     *
     * @throws Exception curl error or midtrans error
     *
     * @return string snap token
     */
    public static function getSnapToken($params) {
        return CVendor_Midtrans_Snap::createTransaction($params)->token;
    }

    /**
     * Create Snap URL payment.
     *
     * Example:
     *
     * ```php
     *
     *   namespace Midtrans;
     *
     *   $params = array(
     *     'transaction_details' => array(
     *       'order_id' => rand(),
     *       'gross_amount' => 10000,
     *     )
     *   );
     *   $paymentUrl = Snap::getSnapUrl($params);
     * ```
     *
     * @param array $params Payment options
     *
     * @throws Exception curl error or midtrans error
     *
     * @return string snap redirect url
     */
    public static function getSnapUrl($params) {
        return CVendor_Midtrans_Snap::createTransaction($params)->redirect_url;
    }

    /**
     * Create Snap payment page, with this version returning full API response.
     *
     * Example:
     *
     * ```php
     *   $params = array(
     *     'transaction_details' => array(
     *       'order_id' => rand(),
     *       'gross_amount' => 10000,
     *     )
     *   );
     *   $paymentUrl = Snap::getSnapToken($params);
     * ```
     *
     * @param array $params Payment options
     *
     * @throws Exception curl error or midtrans error
     *
     * @return object snap response (token and redirect_url)
     */
    public static function createTransaction($params) {
        $payloads = [
            'credit_card' => [
                // 'enabled_payments' => array('credit_card'),
                'secure' => CVendor_Midtrans_Config::$is3ds
            ]
        ];

        if (isset($params['item_details'])) {
            $gross_amount = 0;
            foreach ($params['item_details'] as $item) {
                $gross_amount += $item['quantity'] * $item['price'];
            }
            $params['transaction_details']['gross_amount'] = $gross_amount;
        }

        if (CVendor_Midtrans_Config::$isSanitized) {
            CVendor_Midtrans_Sanitizer::jsonRequest($params);
        }

        $params = array_replace_recursive($payloads, $params);

        return CVendor_Midtrans_ApiRequestor::post(
            CVendor_Midtrans_Config::getSnapBaseUrl() . '/transactions',
            CVendor_Midtrans_Config::$serverKey,
            $params
        );
    }
}
