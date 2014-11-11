<?php
return array(
		array(
			"name"=>"purchase_order_menu_list",
			"label"=>"Purchase Order",
			"requirements"=>array(
				"purchase_order"=>true,
			),
			"subnav"=>include dirname(__FILE__)."/transaction/purchase_order.php",
		),
		array(
			"name"=>"receiving_menu_list",
			"label"=>"Receiving",
			"requirements"=>array(
				"have_receiving"=>true,
			),
			"subnav"=>include dirname(__FILE__)."/transaction/receiving.php",
		),
		array(
			"name"=>"purchase_menu_list",
			"label"=>"Purchase",
			"requirements"=>array(
				"have_purchase"=>true,
			),
			"subnav"=>include dirname(__FILE__)."/transaction/purchase.php",
		),
		
		array(
			"name"=>"sales_menu_list",
			"label"=>"Sales",
			"requirements"=>array(
				"have_sales"=>true,
			),
			"subnav"=>include dirname(__FILE__)."/transaction/sales.php",
		),
		array(
			"name"=>"payable_purchase_menu",
			"label"=>"Payable Purchase",
			"requirements"=>array(
				"have_purchase"=>true,
				"have_purchase_credit"=>true,
			),
			"subnav"=>include dirname(__FILE__)."/transaction/payable_purchase.php",
		),
		array(
			"name"=>"receivable_sales_menu",
			"label"=>"Receivable Sales",
			"requirements"=>array(
				"have_sales"=>true,
				"have_sales_credit"=>true,
			),
			"subnav"=>include dirname(__FILE__)."/transaction/receivable_sales.php",
		),
		array(
			"name"=>"payable_other_menu",
			"label"=>"Other Payable",
			"requirements"=>array(
				"have_payable_other"=>true,
			),
			"subnav"=>array(
				array(
					"name"=>"payable_other_list",
					"label"=>"Other Payable",
					"controller"=>"payable_other",
					"method"=>"index",
					
				),
				
			),
		),
		array(
			"name"=>"receivable_other_menu",
			"label"=>"Other Receivable",
			"requirements"=>array(
				"have_receivable_other"=>true,
			),
			"subnav"=>array(
				array(
					"name"=>"receivable_other_list",
					"label"=>"Other Receivable",
					"controller"=>"receivable_other",
					"method"=>"index",
					
				),
				
			),
		),
		array(
			"name"=>"payable_generate_menu",
			"label"=>"Payable Generate",
			"requirements"=>array(
				"have_payable_generate"=>true,
			),
			"subnav"=>array(
				array(
					"name"=>"payable_generate_list",
					"label"=>"Payable Generate",
					"controller"=>"payable_generate",
					"method"=>"index",
					
				),
				
			),
		),
		array(
			"name"=>"expense_menu",
			"label"=>"Expense",
			"requirements"=>array(
				"have_expense"=>true,
			),
			"subnav"=>include dirname(__FILE__)."/transaction/expense.php",
		),
	);//end subnav transaction_menu
	
	