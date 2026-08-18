<?php

class CVendor_Xendit_Balance extends CVendor_Xendit_Base {
    public function classUrl() {
        return  '/balance';
    }

    /**
     * Instantiate required params for Create.
     *
     * @return array
     */
    public function createReqParams() {
        return [];
    }

    /**
     * Instantiate required params for Update.
     *
     * @return array
     */
    public function updateReqParams() {
        return [];
    }

    /**
     * Available account type.
     *
     * @return array
     */
    public static function accountType() {
        return ['CASH', 'HOLDING', 'TAX'];
    }

    /**
     * Available currency.
     *
     * @return array
     */
    public static function currency() {
        return ['IDR', 'PHP', 'USD'];
    }

    /**
     * Validation for account type.
     *
     * @param string $accountType Account type
     *
     * @return void
     */
    public static function validateAccountType($accountType = null) {
        if (!in_array($accountType, self::accountType())) {
            $msg = 'Account type is invalid. Available types: CASH, TAX, HOLDING';

            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * Validation for account type.
     *
     * @param string $currency
     *
     * @return void
     */
    public static function validateCurrency($currency = null) {
        if (!in_array($currency, self::currency())) {
            $msg = 'Currency is invalid. Available currency: ' . carr::implodes(',', self::currency());

            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * Send GET request to retrieve data.
     *
     * @param string     $accountType account type (CASH|HOLDING|TAX)
     * @param null|mixed $currency
     *
     * @throws CVendor_Xendit_Exception_ApiException
     *
     * @return array[
     *                'balance' => int
     *                ]
     */
    public function getBalance($accountType = null, $currency = 'IDR') {
        self::validateAccountType($accountType);
        self::validateCurrency($currency);
        $query = carr::query([
            'account_type' => $accountType,
            'currency' => $currency
        ]);

        $url = $this->classUrl() . '?' . $query;

        return $this->request('GET', $url);
    }
}
