<?php
return array(
		array(
			"name"=>"analyze_site",
			"label"=>"Site Analyze",
			"controller"=>"analyze_site",
			"method"=>"index",
			"requirements"=>array(
				"have_analyze_site"=>true,
			),
		),
		array(
			"name"=>"analyze_item",
			"label"=>"Item Analyze",
			"controller"=>"analyze_item",
			"method"=>"index",
			"requirements"=>array(
				"have_analyze_item"=>true,
			),
		),
		array(
			"name"=>"analyze_purchase",
			"label"=>"Purchase Analyze",
			"controller"=>"analyze_purchase",
			"method"=>"index",
			"requirements"=>array(
				"have_analyze_purchase"=>true,
			),
		),
		array(
			"name"=>"analyze_sales",
			"label"=>"Sales Analyze",
			"controller"=>"analyze_sales",
			"method"=>"index",
			"requirements"=>array(
				"have_analyze_sales"=>true,
			),
		),
		array(
			"name"=>"analyze_purchase_payable",
			"label"=>"Purchase Payable Analyze",
			"controller"=>"analyze_purchase_payable",
			"method"=>"index",
			"requirements"=>array(
				"have_analyze_purchase_payable"=>true,
			),
			
		),
		array(
			"name"=>"analyze_sales_receivable",
			"label"=>"Sales Receivable Analyze",
			"controller"=>"analyze_sales_receivable",
			"method"=>"index",
			"requirements"=>array(
				"have_analyze_sales_receivable"=>true,
			),
			
		),
	);