<?php

class CVendor_Namecheap_Command_Users_Address extends CVendor_Namecheap_AbstractCommand {
    protected $command = 'namecheap.users.address.';

    /**
     * @todo Creates a new address for the user
     */
    public function create(array $param) {
        $requiredParams = ['AddressName', 'EmailAddress', 'FirstName', 'LastName', 'Address1', 'City', 'StateProvince', 'StateProvinceChoice', 'Zip', 'Country', 'Phone'];
        $data = [
            'AddressName' => !empty($param['addressName']) ? $param['addressName'] : null,
            'EmailAddress' => !empty($param['emailAddress']) ? $param['emailAddress'] : null,
            'FirstName' => !empty($param['firstName']) ? $param['firstName'] : null,
            'LastName' => !empty($param['lastName']) ? $param['lastName'] : null,
            'Address1' => !empty($param['address1']) ? $param['address1'] : null,
            'City' => !empty($param['city']) ? $param['city'] : null,
            'StateProvince' => !empty($param['stateProvince']) ? $param['stateProvince'] : null,
            'StateProvinceChoice' => !empty($param['stateProvinceChoice']) ? $param['stateProvinceChoice'] : null,
            'Zip' => !empty($param['zip']) ? $param['zip'] : null,
            'Country' => !empty($param['country']) ? $param['country'] : null,
            'Phone' => !empty($param['phone']) ? $param['phone'] : null,
            'DefaultYN' => !empty($param['defaultYN']) ? $param['defaultYN'] : null,
            'JobTitle' => !empty($param['jobTitle']) ? $param['jobTitle'] : null,
            'Organization' => !empty($param['organization']) ? $param['organization'] : null,
            'Address2' => !empty($param['address2']) ? $param['address2'] : null,
            'PhoneExt' => !empty($param['phoneExt']) ? $param['phoneExt'] : null,
            'Fax' => !empty($param['fax']) ? $param['fax'] : null,
        ];
        $reqFields = $this->api->checkRequiredFields($data, $requiredParams);
        if (count($reqFields)) {
            $flist = implode(', ', $reqFields);

            throw new \Exception($flist . ' : these fields are required!', 2010324);
        }

        return $this->api->get($this->command . __FUNCTION__, $data);
    }

    /**
     * @todo Deletes the particular address for the user.
     *
     * @param int $addressId The unique AddressID to delete
     */
    public function delete($addressId) {
        return $this->api->get($this->command . __FUNCTION__, ['AddressID' => $addressId]);
    }

    /**
     * @todo Gets information for the requested addressID.
     *
     * @param int $addressId The unique AddressID
     */
    public function getInfo($addressId) {
        return $this->api->get($this->command . __FUNCTION__, ['AddressID' => $addressId]);
    }

    /**
     * @todo Gets a list of addressIDs and addressnames associated with the user account.
     */
    public function getList() {
        return $this->api->get($this->command . __FUNCTION__);
    }

    /**
     * @todo Sets default address for the user.
     *
     * @param int $addressId The unique addressID to set default
     */
    public function setDefault($addressId) {
        return $this->api->get($this->command . __FUNCTION__, ['AddressID' => $addressId]);
    }

    /**
     * @todo Updates the particular address of the user
     */
    public function update(array $param) {
        $requiredParams = ['AddressId', 'AddressName', 'EmailAddress', 'FirstName', 'LastName', 'Address1', 'City', 'StateProvince', 'StateProvinceChoice', 'Zip', 'Country', 'Phone'];
        $data = [
            'AddressId' => !empty($param['addressId']) ? $param['addressId'] : null,
            'AddressName' => !empty($param['addressName']) ? $param['addressName'] : null,
            'EmailAddress' => !empty($param['emailAddress']) ? $param['emailAddress'] : null,
            'FirstName' => !empty($param['firstName']) ? $param['firstName'] : null,
            'LastName' => !empty($param['lastName']) ? $param['lastName'] : null,
            'Address1' => !empty($param['address1']) ? $param['address1'] : null,
            'City' => !empty($param['city']) ? $param['city'] : null,
            'StateProvince' => !empty($param['stateProvince']) ? $param['stateProvince'] : null,
            'StateProvinceChoice' => !empty($param['stateProvinceChoice']) ? $param['stateProvinceChoice'] : null,
            'Zip' => !empty($param['zip']) ? $param['zip'] : null,
            'Country' => !empty($param['country']) ? $param['country'] : null,
            'Phone' => !empty($param['phone']) ? $param['phone'] : null,
            'DefaultYN' => !empty($param['defaultYN']) ? $param['defaultYN'] : null,
            'JobTitle' => !empty($param['jobTitle']) ? $param['jobTitle'] : null,
            'Organization' => !empty($param['organization']) ? $param['organization'] : null,
            'Address2' => !empty($param['address2']) ? $param['address2'] : null,
            'PhoneExt' => !empty($param['phoneExt']) ? $param['phoneExt'] : null,
            'Fax' => !empty($param['fax']) ? $param['fax'] : null,
        ];
        $reqFields = $this->api->checkRequiredFields($data, $requiredParams);
        if (count($reqFields)) {
            $flist = implode(', ', $reqFields);

            throw new \Exception($flist . ' : these fields are required!', 2010324);
        }

        return $this->api->get($this->command . __FUNCTION__, $data);
    }
}
