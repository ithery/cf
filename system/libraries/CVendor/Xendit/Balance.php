<?php

class CVendor_Xendit_Balance extends CVendor_Xendit_Base {
    public function classUrl() {
        return  '/balance';
    }

    /**
     * Instantiate required params for Create
     *
     * @return array
     */
    public function createReqParams() {
        return [];
    }

    /**
     * Instantiate required params for Update
     *
     * @return array
     */
    public function updateReqParams() {
        return [];
    }

    /**
     * Available account type
     *
     * @return array
     */
    public static function accountType() {
        return ['CASH', 'HOLDING', 'TAX'];
    }

    /**
     * Validation for account type
     *
     * @param string $account_type Account type
     *
     * @return void
     */
    public static function validateAccountType($account_type = null) {
        if (!in_array($account_type, self::accountType())) {
            $msg = 'Account type is invalid. Available types: CASH, TAX, HOLDING';
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * Send GET request to retrieve data
     *
     * @param string $accountType account type (CASH|HOLDING|TAX)
     *
     * @return array[
     *                'balance' => int
     *                ]
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    public function getBalance($accountType = null) {
        self::validateAccountType($accountType);
        $url = $this->classUrl() . '?account_type=' . $accountType;
        return $this->request('GET', $url);
    }
}
