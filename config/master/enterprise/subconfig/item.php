<?php

$item = array(
	array(
		"name"=>"item_category",
		"label"=>"Item Have Category",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"item_category_code",
		"label"=>"Item Category Have Code",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"item_category"=>true,
		),
	),
	array(
		"name"=>"item_subcategory",
		"label"=>"Item Have Subcategory",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"item_category"=>true,
		),
	),
	array(
		"name"=>"item_subcategory_code",
		"label"=>"Item Subcategory Have Code",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"item_subcategory"=>true,
		),
	),
	array(
		"name"=>"item_code",
		"label"=>"Item Have Code",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"item_code_auto",
		"label"=>"Item Auto Code",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"item_code"=>true,
		),
	),
	array(
		"name"=>"item_code_auto_category_prefix",
		"label"=>"Item Auto Code Using Category Prefix",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"item_code_auto"=>true,
			"item_category"=>true,
		),
	),
	array(
		"name"=>"item_code_auto_subcategory_prefix",
		"label"=>"Item Auto Code Using Subcategory Prefix",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"item_code_auto"=>true,
			"item_subcategory"=>true,
		),
	),
	array(
		"name"=>"item_barcode",
		"label"=>"Item Have Barcode",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"item_brand",
		"label"=>"Have Item Brand",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"item_type",
		"label"=>"Have Item Type",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"item_tag",
		"label"=>"Have Item Tag",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_item_image",
		"label"=>"Have Item Image",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_item_rack",
		"label"=>"Have Item Rack",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"use_stock",
		"label"=>"Using Stock/Inventory",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_item_batch",
		"label"=>"Have Item Batch",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"stock_below_zero",
		"label"=>"Stock Below Zero",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"use_stock"=>true,
		),
	),
	array(
		"name"=>"item_no_stock",
		"label"=>"Have Item No Stock (Services)",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"use_stock"=>true,
		),
	),
	array(
		"name"=>"have_unit",
		"label"=>"Have Unit",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_unit_pcs",
		"label"=>"Have Unit PCS",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_unit"=>true,
		),
	),
	array(
		"name"=>"have_unit_conversion",
		"label"=>"Have Unit Conversion",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_unit"=>true,
		),
	),
	array(
		"name"=>"max_unit_conversion",
		"label"=>"Max Unit Conversion",
		"default"=>'3',
		"type"=>"text",
		"requirement"=>array(
			"have_unit_conversion"=>true,
		),
	),
	array(
		"name"=>"have_unit_conversion_purchase_price",
		"label"=>"Each Unit Have Purchase Price",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_unit_conversion"=>true,
		),
	),
	array(
		"name"=>"have_unit_conversion_sell_price",
		"label"=>"Each Unit Have Sell Price",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_unit_conversion"=>true,
		),
	),
	
	
	array(
		"name"=>"have_item_bom",
		"label"=>"Item Have Bom",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"have_item_must_assembly",
		"label"=>"Have Item Must Assembly",
		"default"=>false,
		"type"=>"checkbox",
		"requirement"=>array(
			"have_item_bom"=>true,
		),
	),
	
	array(
		"name"=>"have_item_profit_percentage",
		"label"=>"Can Set Sales from Profit Percentage",
		"default"=>false,
		"type"=>"checkbox",
	),

	array(
		"name"=>"have_stock_opname_date",
		"label"=>"Have Stock Opname Date",
		"default"=>false,
		"type"=>"checkbox",
	),
	array(
		"name"=>"accounting_calculation",
		"label"=>"Calculation",
		"type"=>"select",
		"default"=>"perpetual",
		"list"=>array(
			"perpetual"=>"Perpetual",
			"period"=>"Period",
		),
	),
);