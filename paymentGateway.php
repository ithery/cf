<?php

    class paymentGateway_Controller extends CB2BController {

        public function __construct() {
            
        }

        public function index() {
            $app = CApp::instance();

            // global setting
            $app->title(clang::__('Payment Gateway'));



            // interface element
            //$app->add_element('pg-start')->set_term_condition(true);
			$app->add_element('pg-start');

            echo $app->render();
        }

        public function __index() {
            $app = CApp::instance();

            // global setting
            $app->title(clang::__('Payment Gateway'));

            // initialization
            $db = CDatabase::instance();

            // interface
            $div_row = $app->add_div()->add_class('row-fluid');
            $div_left = $div_row->add_div()->add_class('span5');
            $div_right = $div_row->add_div()->add_class('span7');
            $widget_left = $div_left->add_widget()->set_nopadding(true)->set_title('Payment Method');
            //get bank image
            $q = $db->query('SELECT code, bank_name,image,image_type FROM bank_account WHERE have_pg = "1" and status = "1"');
            foreach ($q as $k => $v) {
                $div = $widget_left->add_div()->add_class('pg-row');
                if (strlen($v->image) > 0) {
                    $src = "data:image/" . $v->image_type . ";base64," . $v->image . "";
                    $div->add_img()->set_src($src)->add_class("img");
                    $div->add_div()->add_class('pg-arrow icon-chevron-right');
                }
                else {
                    $div->add($v->bank_name);
                }
                $listener = $div->add_listener('click');
                $listener->add_handler('reload')->set_url(curl::base() . 'topup/paymentGateway/widget_right/' . $v->code)->set_target('div-topup-right');
                $listener->add_handler('custom')->set_js("
					jQuery('.pg-row').removeClass('active');
					jQuery(this).addClass('active');");
            }
            $config_file = CF::get_file('data', 'payment_gateway_config');
            $config = include_once $config_file;
            $app->add('<script type="text/javascript" src="' . $config['url_js'] . '"></script>');
            $div_right->add_div('div-topup-right');

            echo $app->render();
        }

        public function process() {
            $app = CApp::instance();

            $db = CDatabase::instance();
            $send = array_merge($_GET, $_POST);
            $topup_id = carr::get($send, 'topup_id');
            $bank_code = carr::get($send, 'bank_code');
            $amount = cdbutils::get_value('SELECT nominal FROM topup_nominal WHERE topup_nominal_id = ' . $db->escape($topup_id));

            $user = $app->user();
//            $auth_id = 'f5ae2cbe70633946840c9a2e7c12f87c';
            $auth_id = ccfg::get('api_auth');
            $generate_pg = ccfg::get('prefix_topup_PG');
            $payment_id = generate_code::get_next_pg_transaction($generate_pg);
//            $payment_id = generate_code::get_next_pg_transaction();
//            $amount = '10000';
            $back_url = curl::base() . 'topup/paymentGateway/notification?id=' . $payment_id;
            $bank_code = '008';
            $bank_product = 'MANDIRIIB';
/*            if (strlen($bank_code) > 0) {
                if ($bank_code == '008') {
                    $bank_product = 'Mandiri';
                }
                else if ($bank_code == '014') {
                    $bank_product = 'BCA';
                }
                else if ($bank_code == '016') {
                    $bank_product = 'BII';
                }
            }*/
            $generate_api_key = ccfg::get('prefix_topup_PG_api_key');
			$transaction_key = generate_code::get_next_transaction_api_key($generate_api_key);

            // insert into pg_transaction
            $data = array(
                "org_id" => $user->org_id,
                "tmaster_id" => $user->tmaster_id,
                "payment_id" => $payment_id,
                //"client_reference_id" => $payment_id,
                //"server_reference_id"    
                "amount" => $amount,
                "back_url" => $back_url,
                "bank_code" => $bank_code,
                "bank_product" => $bank_product,
                "status_transaction" => 'REQUEST',
                //                "is_posted"
                "created" => date('Y-m-d H:i:s'),
                "createdby" => $user->username,
                "updated" => date('Y-m-d H:i:s'),
                "updatedby" => $user->username
            );
            $i = $db->insert("pg_transaction", $data);

// insert to transaction_api_key (reference_transaction_key = biarkan NULL,transaction_key = auto generate, ref_module = TOPUP, ref_table = pg_transaction, ref_id = <pg_transaction_id>)
            $pg_transaction_id = $i->insert_id();
            $data_transaction_api_key = array(
                "org_id" => $user->org_id,
                "tmaster_id" => $user->tmaster_id,
                "transaction_key" => $transaction_key,
                //"reference_transaction_key" => 
                "reference_id" => $pg_transaction_id,
                "reference_table" => 'pg_transaction',
                "reference_module" => 'TOPUP',
                //"is_posted" =>
                "created" => date('Y-m-d H:i:s'),
                "createdby" => $user->username,
                "updated" => date('Y-m-d H:i:s'),
                "updatedby" => $user->username
            );
            $db->insert("transaction_api_key", $data_transaction_api_key);

            // insert to table topup
            $row = cdbutils::get_row('SELECT * FROM bank_account WHERE code = ' . $db->escape($bank_code));
            $err_code = 0;
            $err_message = '';
            $tmaster_id = '';
            $username = null;

            $config_file = CF::get_file('data', 'payment_gateway_config');
            $config = include_once $config_file;

            try {
                $code = generate_code::get_next_topup_code($config['prefix']['TopUp']);

                $data_topup = array(
                    'org_id' => $user->org_id,
                    'topup_type' => 'PG',
                    'bank_account_id' => $row->bank_account_id,
                    'topup_nominal_id' => $topup_id,
                    'code' => $code,
                    'tmaster_id' => $tmaster_id,
                    'payment_from' => '',
                    'bank_name' => $row->bank_name,
                    'acc_no' => $row->acc_no,
                    'acc_name' => $row->acc_name,
                    'currency_code' => 'IDR',
                    'nominal' => $amount,
                    'multiple' => '1',
                    'nominal_total' => $amount,
                    'note' => '',
                    'reason' => '',
                    'status_confirm' => 'Pending',
                    'approved' => '',
                    'confirmed' => '',
                    'expired' => '',
                    'created' => date("Y-m-d H:i:s"),
                    'createdby' => $username,
                    'updated' => date("Y-m-d H:i:s"),
                    'updatedby' => $username,
                    'createdip' => crequest::remote_address(),
                    'updatedip' => crequest::remote_address(),
                    'is_posted' => '0',
                    'status' => '1',
                    'reference_payment_id' => $payment_id,
                );
                $insert_topup = $db->insert('topup', $data_topup);
            }
            catch (Exception $e) {
                $err_code++;
                $err_message = $e->getMessage();
            }

            if ($err_code == 0) {
                cmsg::add("success", clang::__("Request is processing. Please wait ..."));
            }
            else {
                cmsg::add("error", $err_message);
            }

            $src = ccfg::get('api_domain') . 'paymentGateway/plugin?auth_id=' . $auth_id . '&transaction_key=' . $transaction_key . '&payment_id=' . $payment_id . '&amount=' . $amount . '&back_url=' . $back_url . '&bank_code=' . $bank_code . '&bank_product=' . $bank_product;
            $app->add('<iframe id="my_iframe" src="' . $src . '" scrolling="yes" frameborder="0"></iframe>');

            echo $app->render();
        }

        public function WidgetBank($bank_code) {
            $app = CApp::instance();

            // initialization
            $db = CDatabase::instance();
            $bank_name = '';

            //get bank name
            $q = "SELECT code, bank_name,image,image_type FROM bank_account WHERE have_pg = '1' and status = '1' and code=" . $db->escape($bank_code);
            $bank_account = cdbutils::get_row($q);
            if ($bank_account != null) {
                $bank_name = $bank_account->bank_name;
            }

            // interface element
            $app->add_element('pg-widget_bank')->set_bank_code($bank_code);

            echo $app->render();
        }

        public function table_detail_transaction() {
            $app = CApp::instance();

            // interface element
            $app->add_element('pg-table_detail_transaction');

            echo $app->render();
        }

        /**
         * Thank you page.
         * This function is just for show data transaction and status of transaction.
         * payment_id
         */
        public function notification() {
            $app = CApp::instance();

            // global setting
            $app->title(clang::__('Thank You Page'));



            // get payment_id
            $data_topup = array_merge($_GET, $_POST);
            $payment_id = carr::get($data_topup, 'id');

            // interface element
            $app->add_element('pg-notification')->set_payment_id($payment_id);

            echo $app->render();
        }

    }
    