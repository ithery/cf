<?php

return array(
	array(
		"name"=>"report_accounting_journal",
		"label"=>"Journal Report",
		"controller"=>"report_accounting_journal",
		"method"=>"index",
		'action'=>cnav::report_action('accounting_journal'),
		
	),
	array(
		"name"=>"report_accounting_gl",
		"label"=>"General Ledger Report",
		"controller"=>"report_accounting_gl",
		"method"=>"index",
		'action'=>cnav::report_action('accounting_gl'),
		
	),
	
);//end subnav report_accounting_list