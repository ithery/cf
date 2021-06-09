<?php

class CVendor_Xendit_Recurring {
    use CVendor_Xendit_ApiOperation_Request;
    use CVendor_Xendit_ApiOperation_Create;
    use CVendor_Xendit_ApiOperation_Retrieve;
    use CVendor_Xendit_ApiOperation_Update;

    /**
     * Instantiate base URL
     *
     * @return string
     */
    public static function classUrl() {
        return '/recurring_payments';
    }

    /**
     * Instantiate required params for Create
     *
     * @return array
     */
    public static function createReqParams() {
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
    public static function updateReqParams() {
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
    public static function stop($id) {
        $url = '/recurring_payments/' . $id . '/stop!';

        return static::request('POST', $url);
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
     * @throws Exceptions\ApiException
     */
    public static function pause($id) {
        $url = '/recurring_payments/' . $id . '/pause!';

        return static::request('POST', $url);
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
     * @throws Exceptions\ApiException
     */
    public static function resume($id) {
        $url = '/recurring_payments/' . $id . '/resume!';

        return static::request('POST', $url);
    }
}
