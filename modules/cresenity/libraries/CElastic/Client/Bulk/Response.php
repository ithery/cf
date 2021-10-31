<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElastic_Client_Bulk_Response extends CElastic_Client_Response {

    /**
     * @var CElastic_Client_Bulk_Action
     */
    protected $_action;

    /**
     * @var string
     */
    protected $_opType;

    /**
     * @param array|string                  $responseData
     * @param CElastic_Client_Bulk_Action   $action
     * @param string                        $opType
     */
    public function __construct($responseData, CElastic_Client_Bulk_Action $action, $opType) {
        parent::__construct($responseData);
        $this->_action = $action;
        $this->_opType = $opType;
    }

    /**
     * @return CElastic_Client_Bulk_Action
     */
    public function getAction() {
        return $this->_action;
    }

    /**
     * @return string
     */
    public function getOpType() {
        return $this->_opType;
    }

}
