<?php

class CVendor_BCA_Service_BusinessBankingService extends CVendor_BCA_ServiceAbstract {
    /**
     * BalanceInformation.
     *
     * @param string $accountNumber
     *
     * @return array
     */
    public function balanceInformation(string $accountNumber) {
        $requestUrl = "/banking/v3/corporates/{$this->api->getCorporateId()}/accounts/{$accountNumber}";

        return $this->sendRequest('GET', $requestUrl);
    }

    /**
     * AccountStatement.
     *
     * @param string $accountNumber
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    public function accountStatement(string $accountNumber, string $startDate, string $endDate) {
        $requestUrl = "/banking/v3/corporates/{$this->api->getCorporateId()}/accounts/{$accountNumber}/statements?StartDate={$startDate}&EndDate={$endDate}";

        return $this->sendRequest('GET', $requestUrl);
    }

    /**
     * FundTransfer.
     *
     * @param array $fields
     *
     * @return array
     */
    public function fundTransfer(array $fields) {
        $fields = array_merge(
            $fields,
            ['CorporateID' => $this->api->getCorporateId(), 'TransactionDate' => date('Y-m-d')]
        );

        $fields['Remark1'] = strtolower(str_replace(' ', '', $fields['Remark1']));
        $fields['Remark2'] = strtolower(str_replace(' ', '', $fields['Remark2']));

        $requestUrl = '/banking/corporates/transfers';

        return $this->sendRequest('POST', $requestUrl, $fields);
    }
}
