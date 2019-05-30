<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Namecheap API wrapper
 *
 * Method DomainsNs
 * Manage Domains Name server
 *
 * @author Saddam Hossain <saddamrhossain@gmail.com>
 *
 * @version 1
 */
class CVendor_Namecheap_Command_Domains_Ns extends CVendor_Namecheap_AbstractCommand {

    protected $command = 'namecheap.domains.ns.';

    /**
     * @todo Creates a new nameserver.
     *
     * @param str|SLD|req : SLD of the DomainName
     * @param str|TLD|req : TLD of the DomainName
     * @param str|Nameserver|req : Nameserver to create
     * @param str|IP|req : Nameserver IP address
     */
    public function create($std, $tld, $ns, $ip) {
        $data = ['SLD' => $sld, 'TLD' => $tld, 'Nameserver' => $ns, 'IP' => $ip];
        return $this->api->get($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Deletes a nameserver associated with the requested domain.
     * @param str|SLD|Req : SLD of the DomainName
     * @param str|TLD|Req : TLD of the DomainName
     * @param str|Nameserver|Req : Nameserver to delete
     */
    public function delete($std, $tld, $ns) {
        $data = ['SLD' => $sld, 'TLD' => $tld, 'Nameserver' => $ns];
        return $this->api->get($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Retrieves information about a registered nameserver.
     * @param str|SLD|Req : SLD of the DomainName
     * @param str|TLD|Req : TLD of the DomainName
     * @param str|Nameserver|Req : Nameserver
     */
    public function getInfo($std, $tld, $ns) {
        $data = ['SLD' => $sld, 'TLD' => $tld, 'Nameserver' => $ns];
        return $this->api->get($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Updates the IP address of a registered nameserver.
     * @param str|SLD|Req : SLD of the Domain Name
     * @param str|TLD|Req : TLD of the Domain Name
     * @param str|Nameserver|Req : Nameserver
     * @param str|OldIP|Req : Existing IP address
     * @param str|IP|Req : New IP address
     */
    public function update($std, $tld, $ns, $oldIp, $ip) {
        $data = ['SLD' => $sld, 'TLD' => $tld, 'Nameserver' => $ns, 'OldIP' => $oldIp, 'IP' => $ip];
        return $this->api->get($this->command . __FUNCTION__, $data);
    }

}
