<?php

class CVendor_Xendit_Recurring extends CVendor_Xendit_Base {
    use CVendor_Xendit_ApiOperation_Create;
    use CVendor_Xendit_ApiOperation_Retrieve;
    use CVendor_Xendit_ApiOperation_Update;

    /**
     * Instantiate base URL
     *
     * @return string
     */
    protected function classUrl() {
        return '/recurring_payments';
    }

    /**
     * Instantiate required params for Create
     *
     * @return array
     */
    protected function createReqParams() {
        return [
            'external_id',
            'payer_email',
            'description',
            'amount',
            'interval',
            'interval_count'
        ];
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
     * Stop a recurring payment
     *
     * @param string $id recurring payment ID
     *
     * @return array[
     *                'id'=> string,
     *                'user_id'=> string,
     *                'external_id'=> string,
     *                'status'=> 'ACTIVE' || 'STOPPED' || 'PAUSED',
     *                'amount'=> int,
     *                'payer_email'=> string,
     *                'description'=> string,
     *                'interval'=> string,
     *                'interval_count'=> int,
     *                'recurrence_progress'=> int,
     *                'should_send_email'=> bool,
     *                'missed_payment_action'=> string,
     *                'recharge'=> bool,
     *                'created'=> string,
     *                'updated'=> string,
     *                'start_date'=> string
     *                ]
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    public function stop($id) {
        $url = '/recurring_payments/' . $id . '/stop!';

        return $this->request('POST', $url);
    }

    /**
     * Pause a recurring payment
     *
     * @param string $id recurring payment ID
     *
     * @return array[
     *                'id'=> string,
     *                'user_id'=> string,
     *                'external_id'=> string,
     *                'status'=> 'ACTIVE' || 'STOPPED' || 'PAUSED',
     *                'amount'=> int,
     *                'payer_email'=> string,
     *                'description'=> string,
     *                'interval'=> string,
     *                'interval_count'=> int,
     *                'recurrence_progress'=> int,
     *                'should_send_email'=> bool,
     *                'missed_payment_action'=> string,
     *                'recharge'=> bool,
     *                'created'=> string,
     *                'updated'=> string,
     *                'start_date'=> string
     *                ]
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    public function pause($id) {
        $url = '/recurring_payments/' . $id . '/pause!';

        return $this->request('POST', $url);
    }

    /**
     * Resume a recurring payment
     *
     * @param string $id recurring payment ID
     *
     * @return array[
     *                'id'=> string,
     *                'user_id'=> string,
     *                'external_id'=> string,
     *                'status'=> 'ACTIVE' || 'STOPPED' || 'PAUSED',
     *                'amount'=> int,
     *                'payer_email'=> string,
     *                'description'=> string,
     *                'interval'=> string,
     *                'interval_count'=> int,
     *                'recurrence_progress'=> int,
     *                'should_send_email'=> bool,
     *                'missed_payment_action'=> string,
     *                'recharge'=> bool,
     *                'created'=> string,
     *                'updated'=> string,
     *                'start_date'=> string
     *                ]
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    public function resume($id) {
        $url = '/recurring_payments/' . $id . '/resume!';

        return $this->request('POST', $url);
    }
}
