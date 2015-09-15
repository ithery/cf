<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Jan 6, 2015
     * @license http://piposystem.com Piposystem
     */
    class transactionInquiry_Controller extends CController{
        
        private $_err_code = 0;
        private $_err_message = array();
        private $_log_id;
        private $comm_code_fixed;
        private $password_fixed;
        private $pg_config;
        
        public function __construct() {
            $config_file = CF::get_file('data', 'payment_gateway_config');
            $this->pg_config = include_once $config_file;
            $trans_inq = $this->pg_config['trans_inq'];
            $this->comm_code_fixed = $trans_inq['comm_code'];
            $this->password_fixed = $trans_inq['password'];
        }
        
        public function index($post = array()){
            $post = array_merge($_POST, $_GET);
			
			clog::write("post trans:" .json_encode($post));
            $db = CDatabase::instance();
            
            /**
             * Log Request
             */
            try {
                $db->begin();
                $data_log = array(
                    'created' => date("Y-m-d H:i:s"),
                    'action' => 'TransactionInquiry',
                    'request' => serialize($post),
                    'status' => 1,
                );
                $r = $db->insert('scm_payment_log', $data_log);
                $this->_log_id = $r->insert_id();
            }
            catch (Exception $ex) {
                $this->_err_code++;
                $this->_err_message[] = 'DB.Error[1]: ' .$ex->getMessage();
            }
            
            if ($this->_err_code == 0) {
                $db->commit();
            }
            else {
                $db->rollback();
            }
            
            /**
             * Validation Request
             */
            if ($this->_err_code == 0) {
                $this->__validate_request($post);
            }
            
            /**
             * Processing Transaction Inquiry
             */
            $response = array();
            if ($this->_err_code == 0) {
                $result = $this->__process();
                if ($result !== NULL) {
                    $response_data = $result['data'];
                    $response = array(
                        'success_flag' => '0',
                        'err_message' => 'SUCCESS',
                        'order_id'    => $response_data['transaction_id'],
                        'amount'  => $response_data['amount'],
                        'currency' => $response_data['currency_id'],
                        'desc'    => $response_data['desc'],
                        'trx_date' => date('d/m/Y H:i:s', strtotime($response_data['create_time']))
                    );
                    $response = implode(';', $response);
                }
            }
            
            if ($this->_err_code > 0) {
                $response = '1;' .implode(',', $this->_err_message) .';;;;;';
            }
            
            $db_process = TRUE;
            try {
                $db->begin();
                $data_log = array(
                    'updated' => date("Y-m-d H:i:s"),
                    'response' => $response,
                );
                $r = $db->update('scm_payment_log', $data_log, array('scm_payment_log_id' => $this->_log_id));
            }
            catch (Exception $ex) {
                $db_process = FALSE;
                $this->_err_code++;
                $this->_err_message[] = 'DB.Error[2]: ' .$ex->getMessage();
            }
            if ($db_process == TRUE) {
                $db->commit();
            }
            else {
                $db->rollback();
            }
            
            echo $response;
        }
        
        protected function __process(){
            $module = NULL;
            foreach ($this->pg_config['prefix'] as $k => $v) {
                $length = strlen($v);
                if (substr($this->_order_id, 0, $length) == $v) {
                    $module = $k;
                }
            }
            if ($module == NULL) {
                $this->_err_code++;
                $this->_err_message[] = 'Order id is not registered';
                return NULL;
            }
            
            $module = 'PG' .$module .'Module';
            require_once CF::get_dir('libraries') .DS .'PG' .DS .'PGModule' .EXT;
            require_once CF::get_dir('libraries') .DS .'PG' .DS .'Module' .DS .$module .EXT;
            $class = new $module();
            $result = $class->transaction_inquiry($this->_order_id);
            if ($result['err_code'] > 0) {
                $this->_err_code++;
                $this->_err_message[] = $result['err_message'];
                return NULL;
            }
            return $result;
        }

        /**
         * validasi input
         * 
         * @param array $inputs Request Input dari SCM 
         * @return boolean
         */
        private function __validate_request($inputs) {
            
            foreach ($inputs as $key => $value) {
                $inputs[$key] = urldecode($inputs[$key]);
            }
            $this->_rq_uuid = isset($inputs['rq_uuid']) ? $inputs['rq_uuid'] : '';
            $this->_rq_datetime = isset($inputs['rq_datetime']) ? $inputs['rq_datetime'] : '';
            $this->_member_code = isset($inputs['member_code']) ? $inputs['member_code'] : '';
            $this->_comm_code = isset($inputs['comm_code']) ? $inputs['comm_code'] : '';
            $this->_order_id = isset($inputs['order_id']) ? $inputs['order_id'] : '';
            $this->_password = isset($inputs['password']) ? $inputs['password'] : '';
            
            foreach ($this->attributes as $key => $attribute) {
                if (!isset($this->$key)){
                    $this->_err_code++;
                    $this->_err_message[] = substr($key, 1) .' harus di isi';
                }
                else {
                    if (trim($this->$key) == '') {
                        $this->_err_code++;
                        $this->_err_message[] = substr($key, 1) .' tidak boleh berisi kosong';
                    }
                    else {
                        // validation length
                        if (strlen($this->$key) >= $attribute) {
                            $this->_err_code++; 
                            $this->_err_message[] = 'jumlah karakter '.substr($key, 1).' melebihi batas.';
                        }
                    }
                }
            }
            
            if ($this->_err_code == 0) {
                if ($this->_comm_code != $this->comm_code_fixed) {
                    $this->_err_code++;  $this->_err_message[] = "Comm Code Salah";
                }
                else {
                    if ($this->_password != $this->password_fixed) {
                        $this->_err_code++;  $this->_err_message[] = "Password Salah";
                    }
                }
            }
                            
            return NULL;
        }
        
        private $attributes = array(
          '_rq_uuid'        => 50,
          '_rq_datetime'    => 30,
//          '_member_code'    => 20,
          '_comm_code'      => 30,
          '_order_id'       => 30,
          '_password'       => 100
        );
        
        private $_rq_uuid;
        private $_rq_datetime;
        private $_member_code;
        private $_comm_code;
        private $_order_id;
        private $_password;
        
        
        public function generate_test(){
            $value = "1405TP";
            echo strlen($value);
            
            $fields = array();
            $fields['rq_uuid'] = 'rquuid21';
            $fields['rq_datetime'] = '20140923114001';
            $fields['member_code'] = 'JR1';
            $fields['comm_code'] = 'SGWRODEX';
            $fields['order_id'] = 'TP-00030';
            $fields['password'] = 'm4nd1riSupplycHAInR0d3XtoP90233453';
            
            $request = '';
            foreach ($fields as $key => $value) {
                $request .= $key .'=' .$value .'&';
            }
            echo $request;
            
            $resp = $this->index($fields);
            echo $resp;
        }
    }
    