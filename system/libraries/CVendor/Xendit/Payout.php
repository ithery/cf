<?php

class CVendor_Xendit_Payout extends CVendor_Xendit_Base {
    use CVendor_Xendit_ApiOperation_Create;
    use CVendor_Xendit_ApiOperation_Retrieve;

    /**
     * Instantiate relative URL
     *
     * @return string
     */
    protected function classUrl() {
        return '/payouts';
    }

    /**
     * Instantiate required params for Create
     *
     * @return array
     */
    protected function createReqParams() {
        return ['external_id', 'amount', 'email'];
    }

    /**
     * Instantiate required params for Update
     *
     * @return array
     */
    protected function updateReqParams() {
        return [];
    }

    /**
     * Void a payout
     *
     * @param string $id payout ID
     *
     * @return array[
     *                'id'=> string,
     *                'external_id'=> string,
     *                'amount'=> int,
     *                'merchant_name'=> string,
     *                'status'=> 'ISSUED' || 'DISBURSING' || 'VOIDED' || 'LOCKED'
     *                || 'COMPLETED' || 'FAILED',
     *                'expiration_timestamp'=> string,
     *                'created'=> string',
     *                'email'=> string,
     *                'payout_url'=> string
     *                ]
     *
     * @throws Exceptions\ApiException
     */
    public function void($id) {
        $url = $this->classUrl() . '/' . $id . '/void';

        return $this->request('POST', $url);
    }
}
