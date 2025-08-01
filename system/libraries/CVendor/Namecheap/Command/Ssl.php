<?php

class CVendor_Namecheap_Command_Ssl extends CVendor_Namecheap_AbstractCommand {
    protected $command = 'namecheap.ssl.';

    /**
     * @todo Creates a new SSL certificate.
     *
     * ### Possible values for Type parameter:
     * PositiveSSL, EssentialSSL, InstantSSL, InstantSSL Pro, PremiumSSL, EV SSL, PositiveSSL Wildcard, EssentialSSL Wildcard, PremiumSSL Wildcard, PositiveSSL Multi Domain, Multi Domain SSL, Unified Communications, EV Multi Domain SSL
     *
     * @param int         $years         Number of years SSL will be issued for Allowed values: 1,2
     * @param string      $type          SSL product name. See "Possible values for Type parameter" below this list.
     * @param null|int    $sANStoADD     Defines number of add-on domains to be purchased in addition to the default number of domains included into a multi-domain certificate
     * @param null|string $promotionCode Promotional (coupon) code for the certificate
     */
    public function create($years, $type, $sANStoADD = null, $promotionCode = null) {
        $data = [
            'Years' => $years,
            'Type' => $type,
            'SANStoADD' => $sANStoADD,
            'PromotionCode' => $promotionCode,
        ];

        return $this->api->get($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Returns a list of SSL certificates for the particular user.
     *
     * @param null|string $listType   Possible values are ALL,Processing,EmailSent,TechnicalProblem,InProgress, Completed,Deactivated,Active,Cancelled,NewPurchase, NewRenewal. Default Value: All
     * @param null|string $searchTerm Keyword to look for on the SSL list
     * @param null|int    $page       Page to return Default Value: 1
     * @param null|int    $pageSize   Total number of SSL certificates to display in a page. Minimum value is 10 and maximum value is 100. Default Value: 20
     * @param null|string $sortBy     Possible values are PURCHASEDATE, PURCHASEDATE_DESC, SSLTYPE, SSLTYPE_DESC, EXPIREDATETIME, EXPIREDATETIME_DESC,Host_Name, Host_Name_DESC
     */
    public function getList($listType = null, $searchTerm = null, $page = null, $pageSize = null, $sortBy = null) {
        $data = [
            'ListType' => $listType,
            'SearchTerm' => $searchTerm,
            'Page' => $page,
            'PageSize' => $pageSize,
            'SortBy' => $sortBy,
        ];

        return $this->api->get($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Parsers the CSR
     *
     * Possible values for CertificateType parameter:
     * InstantSSL, PositiveSSL, PositiveSSL Wildcard, EssentialSSL, EssentialSSL Wildcard, InstantSSL Pro, PremiumSSL Wildcard, EV SSL, EV Multi Domain SSL, Multi Domain SSL, PositiveSSL Multi Domain, Unified Communications
     *
     * @param string      $csr             Certificate Signing Request
     * @param null|string $certificateType Type of SSL Certificate
     */
    public function parseCSR($csr, $certificateType = null) {
        $data = [
            'csr' => $csr,
            'CertificateType' => $certificateType
        ];

        return $this->api->post($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Gets approver email list for the requested certificate.
     *
     * @param string $domainName      Domain name to get the list
     * @param string $certificateType Type of SSL certificate
     */
    public function getApproverEmailList($domainName, $certificateType) {
        $data = [
            'DomainName' => $domainName,
            'CertificateType' => $certificateType
        ];

        return $this->api->post($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Activates a purchased and non-activated SSL certificate by collecting and validating certificate request data and submitting it to Comodo.
     *
     * @param num|CertificateID|req : Unique identifier of SSL certificate to be activated
     * @param string|CSR|req : Certificate Signing Request (CSR)
     * @param string|AdminEmailAddress|req : Email address to send signed SSL certificate file to
     * @param string|WebServerType|opt : Server software where SSL will be installed. Defines SSL certificate file format (PEM or PKCS7). Allowed values: apacheopenssl, apachessl, apacheraven, apachessleay, apache2, apacheapachessl, tomcat, cpanel, ipswitch, plesk, weblogic, website, webstar, nginx, iis, iis4, iis5, c2net, ibmhttp, iplanet, domino, dominogo4625, dominogo4626, netscape, zeusv3, cobaltseries, ensim, hsphere, other
     *
     * ## Command can be run on purchased and non-activated SSLs in "Newpurchase" or "Newrenewal" status. Use getInfo and getList APIs to collect SSL status.
     * ## Only supported products can be activated. See create API to learn supported products.
     * ## Sandbox limitation: Activation process works for all certificates. However, an actual test certificate will not be returned for OV and EV certificates.
     */
    public function activate() {
        return false;
    }

    /**
     * @todo Resends the approver email.
     *
     * @param string $certificateID The unique certificate ID that you get after calling ssl.create command
     */
    public function resendApproverEmail($certificateID) {
        return $this->api->get($this->command . __FUNCTION__, ['CertificateID' => $certificateID]);
    }

    /**
     * @todo Retrieves information about the requested SSL certificate
     *
     * @param int         $certificateID     Unique ID of the SSL certificate
     * @param null|string $returncertificate A flag for returning certificate in response
     * @param null|string $returntype        Type of returned certificate. Parameter takes “Individual” (for X.509 format) or “PKCS7” values.
     */
    public function getInfo($certificateID, $returncertificate = null, $returntype = null) {
        $data = [
            'CertificateID' => $certificateID,
            'Returncertificate' => $returncertificate,
            'Returntype' => $returntype,
        ];

        return $this->api->get($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Renews an SSL certificate.
     *
     * @param string      $certificateID Unique ID of the SSL certificate you wish to renew
     * @param string      $years         Number of years renewal SSL will be issued for Allowed values: 1,2
     * @param string      $sslType       SSL product name. See "Possible values for SSLType parameter" below this table.
     * @param null|string $promotionCode Promotional (coupon) code for the certificate
     */
    public function renew($certificateID, $years, $sslType, $promotionCode = null) {
        $data = [
            'CertificateID' => $certificateID,
            'Years' => $years,
            'SSLType' => $sslType,
            'PromotionCode' => $promotionCode,
        ];

        return $this->api->post($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Initiates creation of a new certificate version of an active SSL certificate by collecting and validating new certificate request data and submitting it to Comodo.
     */
    public function reissue() {
        return false;
    }

    /**
     * @todo Resends the fulfilment email containing the certificate.
     *
     * @param string $certificateID The unique certificate ID that you get after calling ssl.create command
     */
    public function resendfulfillmentemail($certificateID) {
        return $this->api->get($this->command . __FUNCTION__, ['CertificateID' => $certificateID]);
    }

    /**
     * @todo Purchases more add-on domains for already purchased certificate.
     *
     * @param int $certificateID     ID of the certificate for which you wish to purchase more add-on domains
     * @param int $numberOfSANSToAdd Number of add-on domains to be ordered
     */
    public function purchasemoresans($certificateID, $numberOfSANSToAdd) {
        return $this->api->get($this->command . __FUNCTION__, ['CertificateID' => $certificateID, 'NumberOfSANSToAdd' => $numberOfSANSToAdd]);
    }

    /**
     * @Important This function is currently supported for Comodo certificates only.
     *
     * @todo Revokes a re-issued SSL certificate.
     *
     * Possible values for Type parameter:
     * InstantSSL, PositiveSSL, PositiveSSL Wildcard, EssentialSSL, EssentialSSL Wildcard, InstantSSL Pro, PremiumSSL Wildcard, EV SSL, EV Multi Domain SSL, Multi Domain SSL, PositiveSSL Multi Domain, Unified Communications
     *
     * @param int    $certificateID   ID of the certificate for you wish to revoke Default Value: 1
     * @param string $certificateType Type of SSL Certificate
     */
    public function revokecertificate($certificateID, $certificateType) {
        return $this->api->get($this->command . __FUNCTION__, ['CertificateID' => $certificateID, 'CertificateType' => $certificateType]);
    }

    /**
     * @todo Sets new domain control validation (DCV) method for a certificate or serves as 'retry' mechanism
     */
    public function editDCVMethod() {
        return false;
    }
}
