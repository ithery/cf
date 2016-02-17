<?php

class CDashboard_LastUserOnline extends CElement_Dashboard {

    public function __construct($id="",$options=array()) {
        parent::__construct($id,$options);
    }
    public static function factory($id="",$options=array()) {
        return new CDashboard_LastUserOnline($id,$options);
    }

    
    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $db = CDatabase::instance();
      
        
        $total_user = $this->opt('total_user');
        
        if($total_user == null) $total_user = 5;
        $total_user = (int) $total_user;
        
        $title = "Last User Online";
        $widget = $this->add_widget()->set_title($title);
        $widget->set_nopadding(true);
        
        $table = $widget->add_table();
        $table->set_title($title);
        $table->add_column('username')->set_label("Username");
        $table->add_column('last_request')->set_label("Last Online")->set_align('center');
        
     
        $q = "select * from users order by last_request desc limit ".$total_user;
        
        $table->set_data_from_query($q);
        
        $table->cell_callback_func(array(__CLASS__,'cell_callback'), __FILE__);
        $table->set_ajax(false);
        $table->set_apply_data_table(false);
       
        $html->appendln(parent::html($indent));
        return $html->text();
       
    }
    
    public function js($indent=0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
//        $ajax_url = CAjaxMethod::factory()
//                            ->set_type('callback')
//                            ->set_data('callable', array('CElement_Dashboard_ProductSummary', 'ajax'))
//                            ->set_data('type', 'product_confirmed_active_noimage')
//                            ->makeurl();
//        
//        $js->appendln("
//            jQuery('#".$this->id."_product_confirmed_active_noimage').click(function() {
//                $.cresenity.show_dialog('".$this->id."_product_confirmed_active_noimage_dialog','".$ajax_url."','get',{});
//
//
//            });
//
//
//
//        ");
//        
        
        $js->appendln(parent::js($indent));
        return $js->text();
    }
    
    public static function cell_callback($table,$col,$row,$val) {
        if($col=="last_request"){
            return cutils::human_time_diff($val,date('Y-m-d H:i:s'));
        }
        return $val;
    }
    
    public function ajax($data) {
        $app = CApp::instance();
        $request = array_merge($_GET,$_POST);
        $type = cobj::get($data,"type");
        $q = "";
        $title = "";
        switch($type) {
            case 'product_confirmed_active_noimage':
                $title = "Barang Confirmed &amp; Active (No Image)";
                $q = "select p.name, p.sku, p.weight, p.stock, v.name as vendor_name from product as p inner join vendor as v on v.vendor_id=p.vendor_id where p.parent_id is null and p.status>0 and p.product_type_id=1 and p.status_confirm='CONFIRMED' and p.is_active=1 and (p.image_name is null or p.image_name='')";
            break;
            default :
                
            break;
        }
        $app->add('<h2>'.$title.'</h2>');
        $table = $app->add_table();
        $table->set_ajax(true);
        if(strlen($q)>0) {
            $table->set_data_from_query($q);
        }
        $table->add_column('vendor_name')->set_label('Vendor');
        $table->add_column('name')->set_label('Nama');
        $table->add_column('sku')->set_label('SKU');
        $table->add_column('weight')->set_label('Berat')->set_align('right');
        $table->add_column('stock')->set_label('Stock')->set_align('right');
        $table->set_action_style('btn-dropdown');
        $action = $table->add_row_action();
        $action->set_icon('pencil')->set_label('Edit')->set_link_target('_blank');
        $action->set_link(curl::base()."master/product/edit/{product_id}");
        echo $app->render();
    }
}
