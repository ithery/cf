<?php

class CVendor_Xendit_Account extends CVendor_Xendit_Base {
    use CVendor_Xendit_ApiOperation_Create;
    use CVendor_Xendit_ApiOperation_Retrieve;
    use CVendor_Xendit_ApiOperation_RetrieveAll;

    /**
     * Instantiate base URL.
     *
     * @return string
     */
    protected function classUrl() {
        return '/v2/accounts';
    }

    /**
     * Instantiate required params for Create.
     *
     * @return array
     */
    protected function createReqParams() {
        return ['email', 'type'];
    }

    /**
     * Instantiate required params for Update.
     *
     * @return array
     */
    protected function updateReqParams() {
        return [];
    }
}
