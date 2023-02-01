<?php

class CVendor_Namecheap_Command_Domains_Transfer extends CVendor_Namecheap_AbstractCommand {
    protected $command = 'namecheap.domains.transfer.';

    /**
     * @todo Transfers a domain to Namecheap. You can only transfer .biz, .ca, .cc, .co, .co.uk, .com, .com.es, .com.pe, .es, .in, .info, .me, .me.uk, .mobi, .net, .net.pe, .nom.es, .org, .org.es, .org.pe, .org.uk, .pe, .tv, .us domains through API at this time.
     *
     * @param string      $domainName        Domain name to transfer
     * @param string      $years             Number of years to renew after a successful transfer
     * @param string      $eppCode           The EPPCode is required for transferring .biz, .ca, .cc, .co, .com, .com.pe, .in, .info, .me, .mobi, .net, net.pe, .org, .org.pe, .pe, .tv, .us domains only.
     * @param null|string $promotionCode     Promotional (coupon) code for transfer
     * @param null|string $addFreeWhoisguard Adds free Whoisguard for the domain Default Value: Yes
     * @param null|string $wgEnable          Enables free WhoisGuard for the domain Default Value: No
     */
    public function create($domainName, $years, $eppCode, $promotionCode = null, $addFreeWhoisguard = null, $wgEnable = null) {
        $data = [
            'DomainName' => $domainName, 'Years' => $years,
            'EPPCode' => $eppCode, 'PromotionCode' => $promotionCode,
            'AddFreeWhoisguard' => $addFreeWhoisguard, 'WGEnable' => $wgEnable
        ];

        return $this->api->get($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Gets the status of a particular transfer.
     *
     * @param int $transferID The unique Transfer ID which you get after placing a transfer request
     */
    public function getStatus($transferID) {
        return $this->api->get($this->command . __FUNCTION__, ['TransferID' => $transferID]);
    }

    /**
     * @todo Updates the status of a particular transfer. Allows you to re-submit the transfer after releasing the registry lock.
     *
     * @param int    $transferID The unique Transfer ID which you get after placing a transfer request
     * @param string $resubmit   The value 'true' resubmits the transfer
     */
    public function updateStatus($transferID, $resubmit) {
        return $this->api->get($this->command . __FUNCTION__, ['TransferID' => $transferID, 'Resubmit' => $resubmit]);
    }

    /**
     * @todo Gets the list of domain transfers.
     *
     * @param null|mixed $listType
     * @param null|mixed $searchTerm
     * @param null|mixed $page
     * @param null|mixed $pageSize
     * @param null|mixed $sortBy
     */
    public function getList($listType = null, $searchTerm = null, $page = null, $pageSize = null, $sortBy = null) {
        $data = ['ListType' => $listType, 'SearchTerm' => $searchTerm, 'Page' => $page, 'PageSize' => $pageSize, 'SortBy' => $sortBy];

        return $this->api->get($this->command . __FUNCTION__, $data);
    }
}
