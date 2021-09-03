<?php
class CVendor_Xendit_CardlessCredit extends CVendor_Xendit_Base {
    /**
     * Instantiate relative URL
     *
     * @return string
     */
    protected function classUrl() {
        return '/cardless-credit';
    }

    /**
     * Instantiate required params for Create
     *
     * @return array
     */
    protected function createReqParams() {
        return [
            'cardless_credit_type',
            'external_id',
            'amount',
            'payment_type',
            'items',
            'customer_details',
            'shipping_address',
            'redirect_url',
            'callback_url'
        ];
    }

    protected function updateReqParams() {
        return [];
    }

    /**
     * Calculate payment types
     *
     * @param array $params user's parameters
     *
     * @return array
     *
     * @throws ApiException
     */
    public function calculatePaymentTypes($params = []) {
        $requiredParams = [
            'cardless_credit_type',
            'amount',
            'items',
        ];

        $this->validateParams($params, $requiredParams);

        $url = $this->classUrl() . '/payment-types';

        return $this->request('POST', $url, $params);
    }
}
