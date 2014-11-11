<?php
//is_calculated key means will be calculated on client_sync index function
return array (
   
	"resto_menu_category"=>array(
		"child"=>array(
		),
		"label"=>"Menu Category",
	),
	"resto_menu_subcategory"=>array(
		"child"=>array(
		),
		"label"=>"Menu Subcategory",
	),
	"resto_menu"=>array(
		"child"=>array(
			"resto_menu_kitchen"=>array(
				"after_sync"=>array(
				),
			),
		),
		"label"=>"Menu",
	),
	"resto_menu_note"=>array(
		"child"=>array(
		),
		"label"=>"Menu Note",
	),
	"resto_member_type"=>array(
		"child"=>array(
		),
		"label"=>"Member Type",
	   
	),

	"resto_member"=>array(
		"child"=>array(
			"resto_member_member_type"=>array(
			),
		),
		"label"=>"Member",
	),
	"resto_floor"=>array(
		"child"=>array(
		),
		"label"=>"Floor",
	),
	"resto_table"=>array(
		"child"=>array(
		),
		"label"=>"Table",
	),

    
	"resto_kitchen"=>array(
		"child"=>array(
		),
		"label"=>"Kitchen",
	),

   
	"resto_payment_type_group"=>array(
		"child"=>array(
		),
		"label"=>"Payment Type Group",
		
	),
	"resto_payment_type"=>array(
		"child"=>array(
		),
		"label"=>"Payment Type",
		
	),

    
	"resto_promo"=>array(
		"label"=>"Promo",
		"child"=>array(
			"resto_promo_day"=>array(
				
			),
			"resto_promo_payment_type"=>array(
			   
			),
			"resto_promo_member_type"=>array(
				
			),
			"resto_promo_reward_buyget"=>array(
				
			),
			"resto_promo_reward_discount"=>array(
				
			),
			"resto_promo_reward_exception"=>array(
				
			),
		),
		
	),

    
	"resto_transaction"=>array(
		"label"=>"Transaction",
		"child"=>array(
			"resto_transaction_detail"=>array(
				
			),
			"resto_transaction_promo_detail"=>array(
				
			),
			"resto_transaction_promo"=>array(
				
			),
			"resto_transaction_table"=>array(
				
			),
			"resto_transaction_detail_note"=>array(
				
			),
		),
	   
	),
	"resto_payment"=>array(
		"label"=>"Payment",
		"child"=>array(
		),
		
	),
       
)
?>
