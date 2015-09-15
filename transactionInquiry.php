<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Jul 14, 2015
     * @license http://piposystem.com Piposystem
     */
    class transactionInquiry_Controller extends CController {

        public function __construct() {
            parent::__construct();
        }

        public function index($post = array()) {
            $db = CDatabase::instance();
            $request = array_merge($_GET, $_POST, $post);

            
            // Log request,insert to database
//            pg_sgo_log 
            $data_request = array(
                'request' => json_encode($request),
                'action' => 'TRANSACTION_INQUIRY',
            );
            $r = $db->insert('pg_sgo_log', $data_request);
            $pg_sgo_log_id = $r->insert_id();

            $rq_uuid = carr::get($request, 'rq_uuid');
            $rq_datetime = carr::get($request, 'rq_datetime');
            $member_code = carr::get($request, 'member_code');
            $comm_code = carr::get($request, 'comm_code');
            $order_id = carr::get($request, 'order_id');
            $password = carr::get($request, 'password');

            // Process (Validation + check transaction)
            $err_code = 0;
            $err_message = '';
//            if ($err_code == 0) {
//                if (strlen($rq_uuid) == 0) {
//                    $err_code++;
//                    $err_message = "rq_uuid is required";
//                }
//            }
//
//            if ($err_code == 0) {
//                if (strlen($rq_datetime) == 0) {
//                    $err_code++;
//                    $err_message = "rq_datetime is required";
//                }
//            }
//            if ($err_code == 0) {
//                if (strlen($member_code) == 0) {
//                    $err_code++;
//                    $err_message = "member_code is required";
//                }
//            }
            if ($err_code == 0) {
                if (strlen($comm_code) == 0) {
                    $err_code++;
                    $err_message = "comm_code is required";
                }
            }
//            if ($err_code == 0) {
//                if (strlen($order_id) == 0) {
//                    $err_code++;
//                    $err_message = "order_id is required";
//                }
//            }
            if ($err_code == 0) {
                if (strlen($password) == 0) {
                    $err_code++;
                    $err_message = "password is required";
                }
            }

            $client_reference_id = NULL;
            $pg_transaction = CPaymentGateway_Transaction::instance('TransactionInquiry');
           
            if ($err_code == 0) {
                $pg_transaction->exec($request);
                $response = $pg_transaction->response();
                $err_code_response = carr::get($response, 'err_code');
                $err_message_response = carr::get($response, 'err_message_response');
                $data_response = carr::get($response, 'data');
                
                $debit_from = carr::get($response, 'debit_from');
                $debit_from_name = carr::get($response, 'debit_from_name');
                $debit_from_bank = carr::get($response, 'debit_from_bank');
                $credit_to = carr::get($response, 'credit_to');
                $credit_to_name = carr::get($response, 'credit_to_name');
                $credit_to_bank = carr::get($response, 'credit_to_bank');
                $payment_ref = carr::get($response, 'payment_ref');
                $product_code = carr::get($response, 'product_code');

                // check response success or not?
                if ($err_code_response > 0) {
                    $err_code++;
                    $err_message = carr::get($response, 'err_message');
                }
                
                // if success
                // send data transaction inquiry to org (org_id) based on payment_id
                //  what is data transaction inquiry???
//                'rq_uuid' => 'a',
//                'rq_datetime' => 'b',
//                'member_code' => 'c',
//                'comm_code' => 'd',
//                'payment_id' => 'TPPG1507-00095',
//                'password' => 'e',
                if ($err_code_response == 0) {
                    $reconcile_id = carr::get($response, 'reconcile_id');
                    $reconcile_datetime = carr::get($response, 'reconcile_datetime');
                    $data_reponse = array(
//                        'response' => json_encode($response),
                        'reconcile_id' => $reconcile_id,
                        'reconcile_datetime' => $reconcile_datetime,
                        'update_time' => date("Y-m-d H:i:s"),
                    );
                    $r = $db->update('pg_sgo_log', $data_reponse, array('pg_sgo_log_id' => $pg_sgo_log_id));
                    try {
                        $transaction_api_key_id = carr::get($response, 'transaction_api_key_id');
                        $callback = callback::send('pg', $transaction_api_key_id);
                    }
                    catch (Exception $exc) {
                        $err_code++;
                        $err_message = $exc->getMessage();
                    }


                    if ($err_code == 0) {
                        // get client response
                        $response_callback = $callback->response();
                        $response_json = cjson::decode($response_callback);
                    }
                }
            }
            
            if ($err_code > 0) {
                $pg_transaction->set_ext_response_err_code(1);
                $pg_transaction->set_ext_response_err_message($err_message);
            }


            $format_response = $pg_transaction->format_response();
            $data_reponse = array(
                'response' => $format_response,
                'update_time' => date("Y-m-d H:i:s"),
            );
            $r = $db->update('pg_sgo_log', $data_reponse, array('pg_sgo_log_id' => $pg_sgo_log_id));

            echo $format_response;
//            echo $response_callback;
        }

        function test() {
            // generate based on request sgo
            $data = array(
                'rq_uuid' => 'a',
                'rq_datetime' => date("Y-m-d H:i:s"),
                'member_code' => 'c',
                'comm_code' => 'SGWTORSAPI',
                'order_id' => 'TPPG1508-00046',
                'password' => 'SgwTooR$$$4PI',
            );

            // call ur own function
            $response = $this->index($data);
        }

    }
    