<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Jan 7, 2015
     * @license http://piposystem.com Piposystem
     */
    class paymentNotification_Controller extends CController{
        
        private $_err_code;
        private $_err_message = array();
        private $_log_id;
        private $_history_id;
        private $comm_code_fixed;
        private $password_fixed;
        
        public function __construct() {
            
            $config_file = CF::get_file('data', 'payment_gateway_config');
            $this->pg_config = include_once $config_file;
            $trans_inq = $this->pg_config['payment_notif'];
            $this->comm_code_fixed = $trans_inq['comm_code'];
            $this->password_fixed = $trans_inq['password'];
        }
        
        public function index($post = array()){
            $post = array_merge($_POST, $_GET, $post);
            clog::write("post:" .json_encode($post));
            $db = CDatabase::instance();
                        
            /**
             * Log Request
             */
            try {
                $db->begin();
                $data_log = array(
                    'created' => date("Y-m-d H:i:s"),
                    'action' => 'PaymentNotification',
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
            if ($this->_err_code == 0) $db->commit();
            else $db->rollback();
            
            if ($this->_err_code == 0) {
                // Validation Request
                $this->__validate_request($post);
            }
            
            /**
             * =======###################################=========== 
             * =======  Validation payment_ref IF Exists ===========
             * =======###################################===========
             */
            if ($this->_err_code == 0) {
                $sql = 'SELECT payment_ref '
                        . 'FROM scm_payment_history '
                        . 'WHERE payment_ref = ' .$db->escape($this->_payment_ref);
                $db_payment_ref = cdbutils::get_value($sql);
                if ($db_payment_ref !== NULL) {
                    $this->_err_code++;
                    $this->_err_message[] = 'Payment telah dilakukan';
                }
            }
            
            
            /**
             * Write history to database
             */
            if ($this->_err_code == 0) {
                try {
                    $db->begin();
                    $data_hist = $this->__build_data_history();
                    $r_history = $db->insert('scm_payment_history', $data_hist);
                    $this->_history_id = $r_history->insert_id();
                }
                catch (Exception $ex) {
                    $this->_err_code++;
                    $this->_err_message[] = 'DB.Error[3]: ' .$ex->getMessage();
                }
                if ($this->_err_code == 0) $db->commit();
                else $db->rollback();
            }
            
            $reconcile_id = '';
            $reconcile_datetime = '';
            /**
             * =======#################################=========== 
             * =======  Processing Transaction Inquiry ===========
             * =======#################################===========
             */
            $response = array();
            if ($this->_err_code == 0) {
                $result = $this->__process();
                if ($result !== NULL) {
                    $response_data = $result['data'];
                    $reconcile_datetime = $response_data['reconcile_datetime'];
                    $reconcile_id = md5($reconcile_datetime .json_encode($response_data));
                                    
                    $response = array(
                        'success_flag' => '0',
                        'error_message' => 'SUCCESS',
                        'reconcile_id' => $reconcile_id,
                        'order_id' => $response_data['transaction_id'],
                        'reconcile_datetime' => $reconcile_datetime
                    );
                    $response = implode(',', $response);
                }
            }
            
            if ($this->_err_code > 0) {
                $response = '1,' .implode(',', $this->_err_message) .',,,';
            }
//            clog::write(array('level' => 'ERROR', 'message' => 'Error:' .print_r($this->_err_message, TRUE)));
//            clog::write('LogID:' .$this->_log_id);
            
            /**
             * ========#################################==================
             * ======== Update log for response to bank ==================
             * ========#################################==================
             */
            $db_process = TRUE;
            try {
                $db->begin();
                $data_log = array(
                    'updated' => date("Y-m-d H:i:s"),
                    'response' => $response,
                    'reconcile_id' => $reconcile_id,
                    'reconcile_datetime' => $reconcile_datetime
                );
                $r = $db->update('scm_payment_log', $data_log, array('scm_payment_log_id' => $this->_log_id));
            }
            catch (Exception $ex) {
                $db_process = FALSE;
                $this->_err_code++;
                $this->_err_message[] = 'DB.Error[2]: ' .$ex->getMessage();
            }
            if ($db_process == TRUE) $db->commit();
            else $db->rollback();
            
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
            
            $card = "debit";
            if (isset($this->_approval_code_full_bca) && $this->_approval_code_full_bca != "") $card = "credit_card";
             
            $data_request = array();
            $data_request['bank_code'] = $this->_debit_from_bank;
            $data_request['amount'] = $this->_amount;
            $data_request['card'] = $card;
            
            require_once CF::get_dir('libraries') .DS .'PGEngine' .EXT;
            $r_calculation = PGEngine::factory(strtolower($module), $data_request)->calculation();
            if ($r_calculation['err_code'] > 0) {
                $this->_err_code++;
                $this->_err_message[] = $r_calculation['err_message'];
                return NULL;
            }
            $charge = $r_calculation['data'][$card];
            
            /**
             * Load all library
             */
            $module = 'PG' .$module .'Module';
            require_once CF::get_dir('libraries') .DS .'PG' .DS .'PGPaymentNotif' .EXT;
            require_once CF::get_dir('libraries') .DS .'PG' .DS .'PGModule' .EXT;
            require_once CF::get_dir('libraries') .DS .'PG' .DS .'Module' .DS .$module .EXT;
            
            /**
             * Set data that will be send to each module
             */
            $payment_notif = PGPaymentNotif::factory();
            $payment_notif->amount = $this->_amount;
            $payment_notif->charge = $charge;
            $payment_notif->currency = $this->_ccy;
            $payment_notif->history_id = $this->_history_id;
            $payment_notif->order_id = $this->_order_id;
            $payment_notif->member_id = $this->_member_id;
            $payment_notif->bank_code = $this->_debit_from_bank;
            $payment_notif->card = 'debit';
            if (strlen($this->_approval_code_full_bca) > 0) $payment_notif->card = 'credit_card';
                    
            $class = new $module();
            $result = $class->payment_notification($payment_notif);
            if ($result['err_code'] > 0) {
                $this->_err_code++;
                $this->_err_message[] = $result['err_message'];
                return NULL;
            }
            return $result;
        }
        
        private function __validate_request($inputs) {
            foreach ($inputs as $key => $value) {
                $inputs[$key] = urldecode($inputs[$key]);
            }
            $this->_rq_uuid = isset($inputs['rq_uuid']) ? $inputs['rq_uuid'] : '';
            $this->_rq_datetime = isset($inputs['rq_datetime']) ? $inputs['rq_datetime'] : '';
            $this->_member_id = isset($inputs['member_id']) ? $inputs['member_id'] : '';
            $this->_comm_code = isset($inputs['comm_code']) ? $inputs['comm_code'] : '';
            $this->_ccy = isset($inputs['ccy']) ? $inputs['ccy'] : '';
            $this->_amount = isset($inputs['amount']) ? $inputs['amount'] : '';
            $this->_debit_from = isset($inputs['debit_from']) ? $inputs['debit_from'] : '';
            $this->_debit_from_name = isset($inputs['debit_from_name']) ? $inputs['debit_from_name'] : '';
            $this->_credit_to = isset($inputs['credit_to']) ? $inputs['credit_to'] : '';
            $this->_credit_to_name = isset($inputs['credit_to_name']) ? $inputs['credit_to_name'] : '';
            $this->_message = isset($inputs['message']) ? $inputs['message'] : '';
            $this->_payment_datetime = isset($inputs['payment_datetime']) ? $inputs['payment_datetime'] : '';
            $this->_payment_ref = isset($inputs['payment_ref']) ? $inputs['payment_ref'] : '';
            $this->_order_id = isset($inputs['order_id']) ? $inputs['order_id'] : '';
            $this->_password = isset($inputs['password']) ? $inputs['password'] : '';
            $this->_debit_from_bank = isset($inputs['debit_from_bank']) ? $inputs['debit_from_bank'] : '';
            $this->_credit_to_bank = isset($inputs['credit_to_bank']) ? $inputs['credit_to_bank'] : '';
            $this->_approval_code_full_bca = isset($inputs['approval_code_full_bca']) ? $inputs['approval_code_full_bca'] : '';
            $this->_product_code = isset($inputs['product_code']) ? $inputs['product_code'] : '';
            $this->_product_value = isset($inputs['product_value']) ? $inputs['product_value'] : '';
            $this->_payment_remark = isset($inputs['payment_remark']) ? $inputs['payment_remark'] : '';
            
            foreach ($this->attributes as $key => $attribute) {
                if ($key != '_message') {
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
                            if (strlen($this->$key) > $attribute) {
                                $this->_err_code++;
                                $this->_err_message[] = 'jumlah karakter '.substr($key, 1).' melebihi batas.';
                            }
                        }
                    }
                } // if ($this->$key != '_message')
                else {
                    if (strlen($this->$key) >= 0 && strlen($this->$key) >= $attribute) {
                        $this->_err_code++;
                        $this->_err_message[] = 'jumlah karakter '.substr($key, 1).' melebihi batas.';
                    }
                }
            }
            
            if ($this->_err_code == 0) {
                if ($this->_comm_code != $this->comm_code_fixed) {
                    $this->_err_code++; $this->_err_message[] = "Comm Code Salah";
                }
                else {
                    if ($this->_password != $this->password_fixed) {
                        $this->_err_code++;  $this->_err_message[] = "Password Salah";
                    }
                }
            }
            
            return NULL;
        }
        
        private function __build_data_history(){
            $data = array();
            $data['scm_payment_log_id'] = $this->_log_id;
            $data['rq_uuid'] = $this->_rq_uuid;
            $data['rq_datetime'] = $this->_rq_datetime;
            $data['member_id'] = $this->_member_id;
            $data['comm_code'] = $this->_comm_code;
            $data['sender_id'] = $this->_sender_id;
            $data['receiver_id'] = $this->_receiver_id;
            $data['member_code'] = $this->_member_code;
            $data['ccy'] = $this->_ccy;
            $data['amount'] = $this->_amount;
            $data['debit_from'] = $this->_debit_from;
            $data['debit_from_name'] = $this->_debit_from_name;
            $data['debit_from_bank'] = $this->_debit_from_bank;
            $data['credit_to'] = $this->_credit_to;
            $data['credit_to_name'] = $this->_credit_to_name;
            $data['credit_to_bank'] = $this->_credit_to_bank;
            $data['payment_datetime'] = $this->_payment_datetime;
            $data['payment_ref'] = $this->_payment_ref;
            $data['payment_remark'] = $this->_payment_remark;
            $data['order_id'] = $this->_order_id;
            $data['product_code'] = $this->_product_code;
            $data['product_value'] = $this->_product_value;
            $data['message'] = $this->_message;
            $data['approval_code_full_bca'] = $this->_approval_code_full_bca;
            $data['status'] = '1';
            return $data;
        }
        
        private $attributes = array(
          '_rq_uuid'            => 50,
          '_rq_datetime'        => 30,
          '_member_id'          => 20,
          '_comm_code'          => 30,
          '_ccy'                => 3,
          '_amount'             => 17,
          '_debit_from'         => 19,
          '_debit_from_name'    => 64,
          '_credit_to'          => 19,
//          '_credit_to_name'     => 64,
          '_message'            => 10, // optinal
          '_payment_datetime'   => 100, // datetime
          '_payment_ref'        => 20,
          '_order_id'           => 20,
          '_password'           => 100,
          '_debit_from_bank'    => 20,
          '_credit_to_bank'     => 20
        );
        
        private $_rq_uuid;
        private $_rq_datetime;
        private $_sender_id;
        private $_receiver_id;
        private $_member_id;
        private $_member_code;
        private $_comm_code;
        private $_ccy;
        private $_amount;
        private $_debit_from;
        private $_debit_from_name;
        private $_credit_to;
        private $_credit_to_name;
        private $_message;
        private $_payment_datetime;
        private $_payment_ref;
        private $_order_id;
        private $_password;
        private $_debit_from_bank;
        private $_credit_to_bank;
        private $_approval_code_full_bca;
        private $_payment_remark;
        private $_product_code;
        private $_product_value;
        
        
        public function generate_test(){
            $fields = array();
            $fields['rq_uuid'] = 'rquuid21';
            $fields['rq_datetime'] = '20140923114001';
            $fields['member_id'] = 'BMDSUB01';
            $fields['order_id'] = 'TPTH1507-00004';
            $fields['comm_code'] = 'SGWTHERABUANA';
            $fields['ccy'] = 'IDR';
            $fields['amount'] = 500000;
            $fields['debit_from'] = 'qwe';
            $fields['debit_from_name'] = 'qwe';
            $fields['credit_to'] = 'ccc';
            $fields['credit_to_name'] = 'abc';
            $fields['payment_datetime'] = '20140923114001';
            $fields['payment_ref'] = 'testing321za23';
            $fields['password'] = 'SGWTheR4Bu4NAR0deXX';
            $fields['debit_from_bank'] = '008';
            $fields['credit_to_bank'] = '008';
            
            $request = '';
            foreach ($fields as $key => $value) {
                $request .= $key .'=' .$value .'&';
            }
            echo "REQUEST <br/>";
            echo $request;
            
            echo '<br/>';
            echo "RESPONSE <br/>";
            $resp = $this->index($fields);
            echo $resp;
//            $response_arr = array(
//                'success_flag' => 0,
//                'error_message' => 'SUCCESS',
//                'reconcile_id' => 'ASD',
//                'order_id' => '123',
//                'reconcile_datetime' => 'asd'
//            );
//            $response = implode(';', $response_arr);
//            echo $response;
        }
    }
    
    