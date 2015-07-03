<?php


return array(
	array(
		"name"=>"dashboard",
		"label"=>"Dashboard",
		"controller"=>"home",
		"method"=>"index",
		"icon"=>"home",
		'action'=>array(
			array(
				'name'=>'widget_transaction_count_graph',
				'label'=>'Transaction Count Graph',
				"requirements"=>array(
					"have_widget_transaction_count_graph"=>true,
				),
			),
			array(
				'name'=>'widget_transaction_amount_graph',
				'label'=>'Transaction Amount Graph',
				"requirements"=>array(
					"have_widget_transaction_amount_graph"=>true,
				),
			),
			array(
                'name'=>'widget_sales_due_date_reminder',
                'label'=>'Sales Due Date Reminder',
                "requirements"=>array(
                    "have_widget_sales_due_date_reminder"=>true,
                ),
            ),
            array(
                'name'=>'widget_purchase_due_date_reminder',
                'label'=>'Purchase Due Date Reminder',
                "requirements"=>array(
                    "have_widget_purchase_due_date_reminder"=>true,
                ),
            ),
            array(
                'name'=>'widget_sales_giro_reminder',
                'label'=>'Sales Giro Reminder',
                "requirements"=>array(
                    "have_widget_sales_giro_reminder"=>true,
                ),
            ),
            array(
                'name'=>'widget_purchase_giro_reminder',
                'label'=>'Purchase Giro Reminder',
                "requirements"=>array(
                    "have_widget_purchase_giro_reminder"=>true,
                ),
            ),
			
			
		),
		
	),
	array(
		"name"=>"resto_menu_list",
		"label"=>"Restaurant",
		"icon"=>"food",
		"subnav"=>include dirname(__FILE__)."/subnav/resto.php",
		"requirements"=>array(
			"have_resto_store"=>true,
		),
	),
	array(
		"name"=>"item_menu",
		"label"=>"Item",
		"icon"=>"gift",
		"subnav"=>include dirname(__FILE__)."/subnav/item.php",
	),
	array(
		"name"=>"contact_menu",
		"label"=>"Contact",
		"icon"=>"book",
		"subnav"=>include dirname(__FILE__)."/subnav/contact.php",
	), //end contact_menu
	array(
		"name"=>"transaction_menu",
		"label"=>"Transaction",
		"icon"=>"shopping-cart",
		"subnav"=>include dirname(__FILE__)."/subnav/transaction.php",
	),
	array(
		"name"=>"cost_menu",
		"label"=>"Cost",
		"icon"=>"list-alt",
		"subnav"=>include dirname(__FILE__)."/subnav/cost.php",
	),
	array(
		
		"name"=>"cash_bank_menu",
		"label"=>"Cash Bank",
		"icon"=>"money",
		"subnav"=>include dirname(__FILE__)."/subnav/cash_bank.php",
		"requirements"=>array(
			"have_finance"=>true,
		),
	),
	array(
		"name"=>"accounting_menu",
		"label"=>"Accounting",
		"icon"=>"usd",
		"subnav"=>include dirname(__FILE__)."/subnav/accounting.php",
		"requirements"=>array(
			"have_accounting"=>true,
		),
	),
	array(
		"name"=>"report_menu",
		"label"=>"Report",
		"icon"=>"file",
		"subnav"=>include dirname(__FILE__)."/subnav/report.php",
	),
	array(
		"name"=>"report_analyze",
		"label"=>"Analyze",
		"icon"=>"bar-chart",
		"subnav"=>include dirname(__FILE__)."/subnav/analyze.php",
	),
	array(
		"name"=>"log_list",
		"label"=>"Log",
		"icon"=>"file-text",
		"subnav"=>include dirname(__FILE__)."/subnav/log.php",
	),//end log_list
	array(
		"name"=>"setting_list",
		"label"=>"Setting",
		"icon"=>"cog",
		"subnav"=>include dirname(__FILE__)."/subnav/setting.php",
	),//end setting_list
   
);
