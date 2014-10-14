<?php defined('SYSPATH') OR die('No direct access allowed.');
class Ctlc_Controller extends CController {
	
	public function index() {
		
	}

	public function convertdb() {
		$tlc_org_id=5;
		$tlc_db_name = "cresenit_tlc";

		$db = CDatabase::instance();
		
		
		$config= array (
			'benchmark'     => FALSE,
			'persistent'    => FALSE,
			'connection'    => array
			(
				'type'     => 'mysql',
				'user'     => 'root',
				'pass'     => '',
				'host'     => 'localhost',
				'port'     => FALSE,
				'socket'   => FALSE,
				'database' => $tlc_db_name
			),
			'character_set' => 'utf8',
			'table_prefix'  => '',
			'object'        => TRUE,
			'cache'         => TRUE,
			'escape'        => TRUE
		);
		$dbtlc = CDatabase::instance('dbtlc',$config);
		
		//move store
		/*
		$db->delete('store',array('org_id'=>$tlc_org_id));
		$db->query('alter table store add column temp_id bigint after org_id');
		$q = "
			insert into store(temp_id,org_id,code,name,description,created,createdby,updated,updatedby,status) 
			select store_id,".$db->escape($tlc_org_id).",code,name,description,created,createdby,updated,updatedby,status from ".$tlc_db_name.".store
		";
		
		$db->query($q);
		*/
		//move unit
		$db->delete('unit',array('org_id'=>$tlc_org_id));
		
		$q = "
			insert into unit(org_id,name,created,createdby,updated,updatedby,status) 
			select ".$db->escape($tlc_org_id).",name,created,createdby,updated,updatedby,status from ".$tlc_db_name.".unit
		";
		
		$db->query($q);
		
	}
	
}