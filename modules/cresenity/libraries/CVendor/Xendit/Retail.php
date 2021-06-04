<?php

class CVendor_Xendit_Retail extends CVendor_Xendit_Base {
    use CVendor_Xendit_ApiOperation_Create;
    use CVendor_Xendit_ApiOperation_Retrieve;
    use CVendor_Xendit_ApiOperation_Update;

    /**
     * Instantiate relative URL
     *
     * @return string
     */
    protected function classUrl() {
        return '/fixed_payment_code';
    }

    /**
     * Instantiate required params for Create
     *
     * @return array
     */
    protected function createReqParams() {
        return ['external_id', 'retail_outlet_name', 'name', 'expected_amount'];
    }

    /**
     * Instantiate required params for Update
     *
     * @return array
     */
    protected function updateReqParams() {
        return [];
    }
}
