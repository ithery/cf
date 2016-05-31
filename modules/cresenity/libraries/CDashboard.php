<?php

class CDashboard extends CElement {

    protected $_dashboard = array();
    protected $_elements = array();
    protected $_config = array();
    
    public function __construct($id = '') {
        parent::__construct($id);
        $file = CF::get_file('data', 'dashboard');
        $dashboard = array();
        if (file_exists($file)) {
            $dashboard = include $file;
        }
        if (!is_array($dashboard)) {
            $dashboard = array();
        }
        $this->_dashboard = $dashboard;
        $this->execute();
    }
    
    protected function load_all_config() {
        
    }
    
    public static function have_access($name, $role_id, $app_id) {
        $db = CDatabase::instance();
        $q = 'select * from role_dashboard where dashboard='.$db->escape($name).' and role_id='.$db->escape($role_id);
        if(strlen($app_id)>0) {
            $q.=" and app_id=".$app_id;
        }
        $row = cdbutils::get_row($q);
        
        return $row!==null;
        
    }
    
    protected function execute() {
        $files = CF::get_files('config', 'dashboard');
        $files = array_reverse($files);
        foreach($files as $file) {
            if(file_exists($file)) {
                $config = include $file;
                $this->_config = array_merge($this->_config,$config);
            }
        }
        foreach($this->_dashboard as $k_dashboard=>$v_dashboard) {
            $name = $k_dashboard;
            $type = carr::get($v_dashboard,'type');
            $options = carr::get($v_dashboard,'options');
            if(isset($this->_config[$type])) {
                $class = carr::get($this->_config[$type],'class');
                $role_id = null;
                $app = CApp::instance();
                $role = $app->role();
                $role_name = '';
                if($role!=null) {
                    $role_id=$role->role_id;
                    $role_name=$role->name;
                }
                $app_id = CF::app_id();
               
                if(self::have_access($name, $role_id, $app_id)||strtolower($role_name)=='superadmin') {
                    $this->_dashboard[$k_dashboard]['element'] = $class::factory('capp_dashboard_'.$name,$options);
                }
                
            } else {
                trigger_error('Dashboard '.$type.' undefined');
            }
        }
    }

    public static function factory($id = '') {
        return new CDashboard($id);
    }

    public function html($indent = 0) {
        
        $html = new CStringBuilder();
        $curr_col = 13;
        foreach ($this->_dashboard as $key => $val) {
            $name = $key;
            $element = carr::get($val,'element');
            $col = carr::get($val,'col');
            $height = carr::get($val,'height');
            if($element!=null) {
                if($curr_col+$col>12) {
                    $curr_col = 0;
                    
                    $div = $this->add_div()->add_class('row-fluid')->custom_css('margin-bottom','20px');;
                    
                }
                $div_col = $div->add_div()->add_class('span'.$col);
                $div_container = $div_col->add_div()->add_class('board-item ' .$name);
                //$div_container->add_class('slimscroll');
                if($height==null) {
                    $height = '400px';
                }
                $div_container->custom_css('height',$height);
                $div_container->custom_css('overflow-y','auto');
                $div_container->add($element);
                //$div_col->add(cdbg::var_dump($element,true));
                $curr_col+=$col;
            }
            
            
        }
        $html->appendln(parent::html($indent));
        return $html->text();
    }

}
