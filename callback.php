<?php

    class callback_Controller extends CB2BController {

        public function __construct() {
            
        }

        public function index() {
            $db = CDatabase::instance();
            $request = array_merge($_POST, $_GET);

            $err_code = carr::get($request, 'err_code');
            $err_message = carr::get($request, 'err_message');
            $data = carr::get($request, 'data');

            if ($err_code == 90000) {
                $err_code = 0;
            }
            else if ($err_code != 90000) {
                $err_message = 'FAILED';
            }
            // insert raw request callback_log from server

            $action = carr::get($request, 'action');
            if ($err_code == 0) {
                if (strlen($action) == 0) {
                    $err_code++;
                    $err_message = "action is required";
                }
            }

            $pg_sgo_log_id = NULL;
            $transaction_key = carr::get($data, 'transaction_key');
            $payment_id = carr::get($data, 'payment_id');
            $client_payment_id = carr::get($data, 'client_payment_id');
            $client_transaction_key = carr::get($data, 'client_transaction_key');
            $amount_real = carr::get($data, 'amount');

            $reconcile_id = carr::get($request, 'reconcile_id');
            $reconcile_datetime = carr::get($request, 'reconcile_datetime');

            $data_set = array(
                'action' => $action,
                'create_time' => date('Y-m-d H:i:s'),
                'request' => json_encode($request),
                'reconcile_id' => $reconcile_id,
                'reconcile_datetime' => $reconcile_datetime,
            );
            $insert = $db->insert('pg_sgo_log', $data_set);
            $pg_sgo_log_id = $insert->insert_id();

            if ($action == 'VERIFY_KEY') {

                // update server_transaction_key
                if ($err_code == 0) {
                    try {
                        $data_set = array('reference_transaction_key' => $transaction_key);
                        $data_where = array('transaction_key' => $client_transaction_key);
                        $update = $db->update('transaction_api_key', $data_set, $data_where);
                    }
                    catch (Exception $exc) {
                        $err_code++;
                        $err_message = $exc->getMessage();
                        $db_err_code++;
                        $db_err_message = $err_message;
                    }
                }
                // update reference_id (payment_id server)
                if ($err_code == 0) {
                    try {
                        $update = $db->update('pg_transaction', array('server_reference_id' => $payment_id), array('payment_id' => $client_payment_id));
                    }
                    catch (Exception $exc) {
                        $err_code++;
                        $err_message = $exc->getMessage();
                        $db_err_code++;
                        $db_err_message = $err_message;
                    }
                }

//            check transaction_key & payment_id is exists
                if ($err_code == 0) {
                    $q = 'SELECT * FROM transaction_api_key WHERE reference_transaction_key = ' . $db->escape($transaction_key);
                    $r = cdbutils::get_row($q);

                    if (count($r) == 0) {
                        $err_code++;
                        $err_message = "Error, transaction_key is not EXIST.";
                    }
                }
                if ($err_code == 0) {
                    $q = 'SELECT * FROM pg_transaction WHERE server_reference_id = ' . $db->escape($payment_id);
                    $r = cdbutils::get_row($q);

                    if (count($r) == 0) {
                        $err_code++;
                        $err_message = "Error, payment_id is not EXIST.";
                    }
                }
            }
            if ($action == 'TRANSACTION_INQUIRY') {
                if ($err_code == 0) {
                    $q = 'SELECT * FROM pg_transaction WHERE payment_id = ' . $db->escape($client_payment_id);
                    $r = cdbutils::get_row($q);
                    if (count($r) == 0) {
                        $err_code++;
                        $err_message = 'Client Payment Id is not FOUND';
                    }
                }
            }
            if ($action == 'PAYMENT_NOTIFICATION') {
                if ($err_code == 0) {
                    $q = '  SELECT 
                                * 
                            FROM 
                                transaction_api_key t, pg_transaction p, topup tp
                            WHERE
                                t.reference_id = p.pg_transaction_id
                                AND tp.reference_payment_id = p.payment_id
                                AND t.transaction_key = ' . $db->escape($client_transaction_key);
                    $r = cdbutils::get_row($q);
                    if (count($r) == 0) {
                        $err_code++;
                        $err_message = 'Client Transaction Key is not FOUND';
                    }
                }
				
				// update new format 8 data from callback API
                if ($err_code == 0) {
                    $debit_from = carr::get($data, 'debit_from');
                    $debit_from_name = carr::get($data, 'debit_from_name');
                    $debit_from_bank = carr::get($data, 'debit_from_bank');
                    $credit_to = carr::get($data, 'credit_to');
                    $credit_to_name = carr::get($data, 'credit_to_name');
                    $credit_to_bank = carr::get($data, 'credit_to_bank');
                    $payment_ref = carr::get($data, 'payment_ref');
                    $product_code = carr::get($data, 'product_code');
                    $pg_transaction_id = $r->pg_transaction_id;
                    $data_update = array(
                        'debit_from' => $debit_from,
                        'debit_from_name' => $debit_from_name,
                        'debit_from_bank' => $debit_from_bank,
                        'credit_to' => $credit_to,
                        'credit_to_name' => $credit_to_name,
                        'credit_to_bank' => $credit_to_bank,
                        'payment_ref' => $payment_ref,
                        'product_code' => $product_code,
                    );
                    $db->update('pg_transaction', $data_update, array('pg_transaction_id' => $pg_transaction_id));
                }

                // call pgPrice to get detail charges
/*                if ($err_code == 0) {
                    try {
                        $module = 'Topup';
                        $bank_code = $r->bank_code;
                        $bank_product = $r->bank_product;
                        $amount = $r->amount;
                        $topup_id = $r->topup_id;
//                        $tmaster_pg_price = tmaster_pg_price::get($module, $bank_code, $bank_product, $amount);
                        $tmaster_pg_price = tmaster_pg_price::calculate($module, $topup_id);
                    }
                    catch (Exception $ex) {
                        $err_code++;
                        $err_message = $ex->getMessage();
                    }
                }
*/                // do logic charges, seller_chargeable and charge_key
                // (moved to library)
               

                // update database topup, tors_fee, bank_fee etc
                $data_update = array(
                    'nominal_real' => $amount_real,
                    //'bank_fee' => $bank_fee,
                    'updated' => date("Y-m-d H:i:s"),
                );
                $r = $db->update('topup', $data_update, array('reference_payment_id' => $client_payment_id));
            }

            $response = array(
                'err_code' => $err_code,
                'err_message' => $err_message,
                'data' => $data
            );
          
            try {
                $db->update('pg_sgo_log', array('response' => json_encode($response)), array('pg_sgo_log_id' => $pg_sgo_log_id));
            }
            catch (Exception $exc) {
                $err_code++;
                $err_message = $exc->getMessage();
                $db_err_code++;
                $db_err_message = $err_message;
            }
            echo cjson::encode($response);
        }

    }
    