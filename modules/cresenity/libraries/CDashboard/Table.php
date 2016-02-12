<?php

class CDashboard_Table extends CElement_Dashboard {

    public function __construct($id="",$options=array()) {
        parent::__construct($id,$options);
    }
    public static function factory($id="",$options=array()) {
        return new CDashboard_Table($id,$options);
    }

    
    public function html($indent = 0) {
        $html = new CStringBuilder();
        $title = $this->opt('title');
        $columns = $this->opt('columns');
        $query = $this->opt('query');
      
        $widget = $this->add_widget()->set_title($title);
        $widget->set_nopadding(true);
        
        $table = $widget->add_table();
        $table->set_title($title);
        
        $columns_array = explode(",",$columns);
        
        foreach ($columns_array as $c_key => $v_key) {
            $table->add_column($v_key)->set_label(strtoupper($v_key));
        }
        $table->set_data_from_query($query);
        
        $table->set_ajax(false);
        $table->set_apply_data_table(false);
       
        $html->appendln(parent::html($indent));
        return $html->text();
       
    }
}
