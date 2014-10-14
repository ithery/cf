<?php defined('SYSPATH') OR die('No direct access allowed.');
	
class Enterprise_reset_data_Controller extends CController {
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("Reset Data"));
		$db = CDatabase::instance();
		$data = array(
			'purchase'=>'Purchase',
			'purchase_detail'=>'Purchase Det.',
			'purchase_cost'=>'Purchase Cost',
			'purchase_detail_batch'=>'Purchase Det. Batch',
			'sales'=>'Sales',
			'sales_detail'=>'Sales Det.',
			'sales_cost'=>'Sales Cost',
			'sales_detail_batch'=>'Sales Det. Batch',
			'sales_detail_hpp'=>'Sales Det. Batch',
			'purchase_return'=>'Purchase Return',
			'purchase_return_detail'=>'Purchase Return Det.',
			'purchase_return_detail_batch'=>'Purchase Return Det. Batch',
			'sales_return'=>'Sales Return',
			'sales_return_detail'=>'Sales Return Det.',
			'sales_return_detail_batch'=>'Sales Return Det. Batch',
			'purchase_refund'=>'Purchase Refund',
			'purchase_refund_detail'=>'Purchase Refund Det.',
			'purchase_refund_detail_batch'=>'Purchase Refund Det. Batch',
			'sales_refund'=>'Sales Refund',
			'sales_refund_detail'=>'Sales Refund Det.',
			'sales_refund_detail_batch'=>'Sales Refund Det. Batch',
			'purchase_revision'=>'Purchase Revision',
			'purchase_revision_detail'=>'Purchase Revision Det.',
			'purchase_revision_item'=>'Purchase Revision Item',
			'purchase_revision_original'=>'Purchase Revision Ori.',
			'purchase_revision_cost'=>'Purchase Revision Cost',
			'purchase_revision_cost_detail'=>'Purchase Revision Cost Det.',
			'purchase_revision_cost_original'=>'Purchase Revision Cost Ori.',
			'sales_revision'=>'Sales Revision',
			'sales_revision_detail'=>'Sales Revision Det.',
			'sales_revision_item'=>'Sales Revision Item',
			'sales_revision_original'=>'Sales Revision Ori.',
			'sales_revision_cost'=>'Sales Revision Cost',
			'sales_revision_cost_detail'=>'Sales Revision Cost Det.',
			'sales_revision_cost_original'=>'Sales Revision Cost Ori.',
			'purchase_payment'=>'Purchase Payment',
			'purchase_payment_detail'=>'Purchase Payment Detail',
			'sales_payment'=>'Sales Payment',
			'sales_payment_detail'=>'Sales Payment Detail',
			'debit_note'=>'Debit Note',
			'debit_note_used'=>'Debit Note Used',
			'debit_note_cashing'=>'Debit Note Cashing',
			'debit_note_write_off'=>'Debit Note Write Off',
			'credit_note'=>'Credit Note',
			'credit_note_used'=>'Credit Note Used',
			'credit_note_cashing'=>'Credit Note Cashing',
			'credit_note_write_off'=>'Credit Note Write Off',
			'stock_opname'=>'Stock Opname',
			'stock_opname_detail'=>'Stock Opname Det.',
			'item_transfer'=>'Item Transfer',
			'item_transfer_detail'=>'Item Transfer Det.',
			'item_history'=>'Item History',
			'payable_history'=>'Payable History',
			'receivable_history'=>'Receivable History',
			'resto_menu_sales'=>'Resto Menu Sales',
			'resto_menu_sales_detail'=>'Resto Menu Sales Detail',
			'accounting_journal'=>'Accounting Journal',
			'accounting_journal_detail'=>'Accounting Journal Detail',
			'expense'=>'Expense',
			'cashflow'=>'Cashflow',
			'cashflow_detail'=>'Cashflow Detail',
			'cashflow'=>'Cashflow',
			'cashflow_detail'=>'Cashflow Detail',
			'cashflow_pre'=>'Cashflow',
			'cashflow_pre_detail'=>'Cashflow Detail',
		);
		
		$post = $_POST;
		if($post!=null) {
			$org_id = $post['org_id'];
			foreach($data as $k=>$v) {
				$db->query("delete from ".$db->escape_table($k)." where org_id=".$db->escape($org_id));
			}
			
			$db->query("update item set stock = 0, stock_pcs = 0, purchase_price=0, profit_percent=0,sell_price=0,sell_price_up_percent = 0, avg=0,cog_avg=0,cog_fifo=0,cog_lifo=0 where org_id=".$db->escape($org_id));
			$db->query("update item_warehouse set qty=0,qty_pcs=0,avg=0,cog_avg=0,cog_fifo=0,cog_lifo=0 where  org_id=".$db->escape($org_id));
			$db->query("update supplier set payable_saldo=0,debit_note_saldo=0 where  org_id=".$db->escape($org_id));
			$db->query("update customer set receivable_saldo=0,credit_note_saldo=0 where  org_id=".$db->escape($org_id));
			$db->query("update accounting_coa set debit_balance=0,credit_balance=0 where  org_id=".$db->escape($org_id));
			
			cmsg::add('success','Success Reset Data');
		}
		
		
		$db = CDatabase::instance();
		
		
		$cdb = CJDB::instance();
		$org_list=$cdb->get_list('org','org_id','name');
		foreach($org_list as $k=>$v) {
			$org_id = $k;
			break;
		}
		$get = $_GET;
		if(isset($get["org_id"])) {
			$org_id = $get["org_id"];
		}
		$widget = $app->add_widget()->set_icon('filter')->set_title('Filter');
		$form = $widget->add_form()->set_method('get');
		$form->add_field()->set_label(clang::__('Organization'))->add_control('org_id','select')->set_value($org_id)->set_list($org_list)->set_submit_onchange(true);
		
		$widget = $app->add_widget();
		
		$form = $widget->add_form()->set_method('post');
		
		
		$i=0;
		$total_data = count($data);
		$column = 4;
		$total_column = ceil($total_data/$column);
		$div = $form->add_div()->add_class('row-fluid');
		foreach($data as $k=>$v) {
			if($i==0||$i%$total_column==0) {
				$span = $div->add_div()->add_class('span'.(12/$column));
			}
			$val = cdbutils::get_value("select count(*) from `".$k."` where org_id=".$db->escape($org_id));
			$span->add_field()->set_label($v)->add_control($k,'label')->set_value(ctransform::thousand_separator($val));
			$i++;
			
		}
		
		
		
		
		$form->add_control('org_id','hidden')->set_value($org_id);
		
		$actions = $form->add_div()->add_class('row-fluid')->add_div()->add_class('span12')->add_action_list();
		$actions->set_style('form-action')->add_action('submit')->set_submit('true')->set_label('Reset Data');
		
		

		
		
		
		echo $app->render();
	}

	
	
	
	
	
} // End Home Controller