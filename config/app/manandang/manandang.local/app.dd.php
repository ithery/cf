<?php

//Global Config
$config['app_id']='1';
$config['title']='MANANDANG';
$config['admin_email']='contact@cresenitytech.com';
$config['multilang']=true;
$config['lang']='id';
$config['set_timezone']=true;
$config['default_timezone']='Asia/Jakarta';
$config['have_clock']=false;
$config['top_menu_cashier']=false;
$config['mail_error']=true;
$config['mail_error_smtp']=false;
$config['date_formatted']='Y-m-d';
$config['time_formatted']='H:i:s';
$config['long_date_formatted']='Y-m-d H:i:s';
$config['smtp_host']='';
$config['smtp_port']='';
$config['smtp_secure']='';
$config['smtp_username']='';
$config['smtp_password']='';
$config['smtp_from']='';

//Client Server Config
$config['have_store']=false;
$config['have_resto_store']=false;
$config['have_retail_store']=false;
$config['have_hotel_store']=false;

//Configuration Config
$config['update_last_request']=true;
$config['log_request']=true;

//Contact Config
$config['have_supplier']=true;
$config['have_customer']=true;
$config['have_customer_credit_limit']=false;
$config['have_employee']=true;
$config['have_expedition']=false;
$config['have_salesman']=true;
$config['have_salesman_commission']=true;
$config['have_doctor']=false;
$config['have_business_partner']=false;

//Warehouse Config
$config['have_warehouse']=true;

//Item Config
$config['item_category']=true;
$config['item_category_code']=true;
$config['item_subcategory']=true;
$config['item_subcategory_code']=true;
$config['item_code']=true;
$config['item_code_auto']=true;
$config['item_code_auto_category_prefix']=true;
$config['item_code_auto_subcategory_prefix']=true;
$config['item_barcode']=false;
$config['item_brand']=false;
$config['item_type']=false;
$config['item_tag']=false;
$config['have_item_image']=true;
$config['have_item_rack']=true;
$config['use_stock']=true;
$config['have_item_batch']=false;
$config['stock_below_zero']=true;
$config['item_no_stock']=false;
$config['have_unit']=true;
$config['have_unit_pcs']=false;
$config['have_unit_conversion']=true;
$config['max_unit_conversion']='4';
$config['have_unit_conversion_purchase_price']=false;
$config['have_unit_conversion_sell_price']=false;
$config['have_item_bom']=false;
$config['have_item_must_assembly']=false;
$config['have_item_profit_percentage']=false;
$config['have_stock_opname_date']=false;
$config['accounting_calculation']='perpetual';

//BOM Config
$config['have_bom']=false;
$config['bom_code']=false;
$config['bom_code_auto']=false;
$config['bom_have_unit']=false;
$config['bom_have_unit_output']=false;
$config['bom_have_unit_conversion']=true;
$config['bom_ajax']=false;

//Production Config
$config['have_production']=false;

//Cost Config
$config['have_cost']=false;
$config['have_cost_code']=false;
$config['have_purchase_cost']=false;
$config['have_sales_cost']=false;

//Purchase Config
$config['have_purchase']=true;
$config['purchase_order']=false;
$config['have_date_purchase_order']=false;
$config['have_purchase_order_bill']=false;
$config['have_receiving']=false;
$config['purchase_order_print_mode']='server';
$config['purchase_order_print_after_checkout']=false;
$config['purchase_order_code_format']='';
$config['purchase_order_bill_org']=false;
$config['have_purchase_supplier']=true;
$config['have_purchase_expedition']=false;
$config['have_purchase_credit']=true;
$config['have_purchase_save_last_price']=true;
$config['purchase_code_format']='';
$config['have_purchase_receipt']=false;
$config['have_purchase_landed_cost']=false;
$config['have_purchase_detail_landed_cost']=false;
$config['have_calculate_landed_cost_on_cogs']=false;
$config['have_payable_generate']=false;
$config['have_purchase_refund']=false;
$config['have_debit_note']=true;
$config['have_create_debit_note']=false;
$config['have_purchase_overpayment']=true;
$config['have_produce_debit_note_on_purchase_overpayment']=true;
$config['have_purchase_revision']=true;
$config['have_purchase_item_duplicate']=false;
$config['have_purchase_date']=false;
$config['have_purchase_refund_date']=false;
$config['have_purchase_payment_date']=false;
$config['have_payable_generate_payment_date']=false;
$config['have_debit_note_write_off_date']=false;
$config['have_debit_note_cashing_date']=false;
$config['have_purchase_global_cost']=true;

//Sales Config
$config['have_sales']=true;
$config['sales_order']=false;
$config['sales_ui']='table';
$config['sales_filter_select_ui']='button';
$config['have_sales_customer']=true;
$config['sales_salesman']=true;
$config['have_sales_expedition']=false;
$config['have_sales_credit']=true;
$config['have_sales_save_last_price']=true;
$config['sales_change_price']=true;
$config['sales_change_qty']=true;
$config['sales_item_rounded']=false;
$config['sales_item_rounded_value']='500';
$config['edit_sales']=false;
$config['sales_code_format']='';
$config['sales_change_title']=true;
$config['have_sales_bill']=true;
$config['sales_bill_print_mode']='client';
$config['sales_bill_print_after_checkout']=false;
$config['sales_bill_org']=true;
$config['have_sales_waybill']=true;
$config['sales_waybill_print_mode']='client';
$config['sales_waybill_print_after_checkout']=false;
$config['sales_waybill_org']=true;
$config['have_custom_sales_waybill']=true;
$config['have_sales_receipt']=true;
$config['have_sales_landed_cost']=false;
$config['have_sales_detail_landed_cost']=false;
$config['have_receivable_generate']=false;
$config['have_sales_receipt_bill']=true;
$config['sales_receipt_bill_print_mode']='client';
$config['sales_receipt_bill_org']=true;
$config['have_sales_refund']=false;
$config['have_credit_note']=true;
$config['have_create_credit_note']=false;
$config['have_sales_overpayment']=true;
$config['have_produce_credit_note_on_sales_overpayment']=true;
$config['have_sales_revision']=true;
$config['have_sales_item_duplicate']=false;
$config['have_sales_payment_amount']=false;
$config['have_sales_date']=false;
$config['have_sales_refund_date']=false;
$config['have_sales_payment_date']=false;
$config['have_credit_note_write_off_date']=false;
$config['have_credit_note_cashing_date']=false;
$config['have_sales_global_cost']=true;

//Purchase Return Config
$config['purchase_return']=true;
$config['have_purchase_return_bill']=true;
$config['purchase_return_bill_print_mode']='client';
$config['purchase_return_bill_print_after_checkout']=false;
$config['purchase_return_bill_org']=true;
$config['have_produce_debit_note_on_purchase_return']=true;
$config['have_purchase_return_date']=false;

//Sales Return Config
$config['sales_return']=true;
$config['have_sales_return_bill']=true;
$config['sales_return_bill_print_mode']='client';
$config['sales_return_bill_print_after_checkout']=false;
$config['sales_return_bill_org']=true;
$config['have_produce_credit_note_on_sales_return']=true;
$config['have_sales_return_date']=false;

//Purchase Discount Config
$config['purchase_discount']=true;
$config['have_purchase_discount_bill']=false;
$config['purchase_discount_bill_print_mode']='server';
$config['purchase_discount_bill_print_after_checkout']=false;
$config['purchase_discount_bill_org']=false;

//Sales Discount Config
$config['sales_discount']=true;
$config['have_sales_discount_bill']=false;
$config['sales_discount_bill_print_mode']='server';
$config['sales_discount_bill_print_after_checkout']=false;
$config['sales_discount_bill_org']=false;

//Accounting Config
$config['have_accounting']=false;

//Finance Config
$config['have_finance']=false;
$config['have_bank_in_out']=false;
$config['have_giro_is_cash']=false;
$config['cash_out_code_format']='';
$config['cash_in_code_format']='';
$config['bank_out_code_format']='';
$config['bank_in_code_format']='';

//Payment Type Config
$config['payment_type_purchase_cash']=array('cash');
$config['payment_type_purchase_credit']=array('cash','bank_transfer','giro');
$config['payment_type_sales_cash']=array('cash');
$config['payment_type_sales_credit']=array('cash','bank_transfer','giro');
$config['payment_type_payable']='';
$config['payment_type_receivable']='';

//Receipt Config
$config['printer_type']='lx300';
$config['printer_protocol_name']='cwebrawprint';
$config['server_printer_name']='';

//TM U-220 Printer Setting Config
$config['bill_header_1']='0192939292';
$config['bill_header_2']='';
$config['bill_header_3']='';
$config['bill_header_4']='';
$config['bill_header_5']='';
$config['bill_footer_1']='';
$config['bill_footer_2']='Thank You';
$config['bill_footer_3']='';
$config['bill_footer_4']='';
$config['bill_footer_5']='';
$config['receiptline']='5';

//Report Config
$config['report_stockcard_show_sales']=false;
$config['report_item_show_other_unit']=true;
$config['report_sales_show_cost']=false;
$config['have_report_xls']=true;
$config['have_report_xls_xml']=false;
$config['have_report_csv']=false;
$config['have_report_pdf']=false;

//Analyze Config
$config['have_analyze_site']=true;
$config['have_analyze_item']=true;
$config['have_analyze_purchase']=true;
$config['have_analyze_sales']=true;
$config['have_analyze_purchase_payable']=true;
$config['have_analyze_sales_receivable']=true;

//Log Config
$config['have_log_login']=true;
$config['have_log_login_fail']=false;
$config['have_log_request']=true;
$config['have_log_activity']=true;
$config['have_log_print']=true;

//Dashboard Config
$config['dashboard_info']=true;
$config['dashboard_info_total_item']=true;
$config['dashboard_info_total_customer']=true;
$config['dashboard_info_total_supplier']=true;
$config['dashboard_info_total_purchase']=false;
$config['dashboard_info_total_today_purchase']=true;
$config['dashboard_info_total_sales']=false;
$config['dashboard_info_total_suspended_sales']=false;
$config['dashboard_info_total_today_sales']=true;
$config['dashboard_transaction_count_chart']=true;
$config['dashboard_transaction_total_chart']=true;

//Misc Config
