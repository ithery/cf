<?php

class Client_Sync_Controller extends CController {
   

    public function index() {


        $app = CApp::instance();
        $app->title(clang::__("Client Synchronize"));
        $t_sync_data =  csync::data();
        $sync_data = $t_sync_data;
        $org = $app->org();
        $org_id=$org->org_id;
        $store_id = ccfg::get("store_id");
        $db = CDatabase::instance();
        $form = $app->add_form('form_synchronize');
        
		
		
		$widget = $form->add_widget()->set_nopadding(true)->set_title('Synchronize')->set_icon('repeat');

        $table = $widget->add_table('table_synchronize');
        $table->add_column('module')->set_label(clang::__("Module"));
        //$table->add_column('last_synchronize')->set_label(clang::__("Last Synchronize"));
        $table->add_column('total_record')->set_label(clang::__("Total Record"));
        $data=array();
        foreach($sync_data as $t=>$val){
			$label = $t;
			if(isset($val['label'])) {
				$label = clang::__($val['label']);
			}
            $data[]=array(
                "key"=>$t,
                "module"=>$label,
                "total_record"=>cclient_sync::get_unsync_count($t),
            );
        }

        $table->set_data_from_array($data)->set_key('key');
        $table->set_apply_data_table(false);
        $actedit = $table->add_row_action('synchronize');
        $actedit->set_label("Sync")->set_jsfunc("client_sync_submit")->set_icon("repeat");
		
		$js = "
			function client_sync_submit(key) {
				var form=$('#form_synchronize');
				if(form.hasClass('loading')) return;
				
				form.addClass('loading');
				
				form.find('.widget-content table').hide();
				form.find('.widget-content').append(jQuery('<div>').addClass('div-loading').html('<div style=\"text-align:center;margin-top:75px;margin-bottom:75px;\"><p >Synchronizing '+key.toUpperCase()+'</p><i class=\"icon-spinner icon-spin icon-4x center\"></i></div>'));
				
				jQuery.ajax({
					type: 'post',
					url: '".curl::base()."client_sync/ajax/'+key,
					dataType: 'json',
					data: $('#form_synchronize').serialize()
				}).done(function( data ) {
					var type='error';
					if(data.result==1) {
						type='success';
						window.location.href='".curl::base()."client_sync/index'
					} else {
						var form=$('#form_synchronize');
						form.removeClass('loading');
						form.find('.widget-content .div-loading').remove();
						form.find('.widget-content table').fadeIn();	
					}
					$.cresenity.message(type,data.message);
					
				}).error(function(obj,t,msg) {
					form.removeClass('loading');
					form.find('.widget-content .div-loading').remove();
					form.find('.widget-content table').fadeIn();	
					$.cresenity.message('error','Error, please call administrator... (' + msg + ')');

				});
			}
		";
		$app->add_js($js);
        echo $app->render();
    }
	
	public function ajax($method,$org_id="",$store_id="",$app_code = "") {
		
		set_time_limit(1200);
		$app = CApp::instance();
		if(strlen($org_id)==0) {
			$org = $app->org();
			$org_id=$org->org_id;
		}
		if(strlen($store_id)==0) {
			$store_id = ccfg::get("store_id");
		}
		
        if($app_code==null) {
            $app_code = $app->code();
        }
		$response = array(
			"result"=>'1',
			"message"=>clang::__("Module")." [".$method."] ".clang::__("Successfully Synchronized")." [".date('Y-m-d H:i:s')."]",
		);
		$error=0;
		$method_arr=array($method);
		if($method=='all'){
			$method_arr=cclient_sync::get_module_array();
			
		}
		$sync = CClientSync::factory($org_id,$store_id,csync::data($app_code));
		
		
		foreach($method_arr as $row_method){
			$sync->synchronize($row_method);
			if($sync->is_error()) {
				break;
			}
			
		}
		
		if($sync->is_error()){
			$response['result']=0;
			$response['message']=$sync->error_message();
			cmsg::add('error',$sync->error_message());
		} else {
			cmsg::add('success',$response["message"]);
			clog::activity($app->user()->user_id,'add',$response["message"]);
		}
		cprogress::set_percent(100);
		echo json_encode($response);
	}
}