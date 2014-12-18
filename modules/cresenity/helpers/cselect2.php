<?php
class cselect2 {
	public static function jsonp($query,$key_field,$search_field,$callback,$term,$page,$limit,$callback_function=null) {
	
		$db = CDatabase::instance();
		$q=$query;
		$total = cdbutils::get_row_count_from_base_query($q);
		
		/* Paging */
		$sLimit = "";
		if ( strlen($limit)>0 ) {
			if(strlen($page)>0) {
				$sLimit = "LIMIT ".((intval($page)-1)*10).", ".intval($limit);
			} else {
				$sLimit = "LIMIT ".intval($limit);
			}
		}
		
		
		/* Ordering */
		$sOrder = "";
		/*
		if ( isset( $_GET['iSortCol_0'] ) ) {
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
					$sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]->fieldname."` ".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" ) {
				$sOrder = "";
			}
		}
		
		*/
		
		$sWhere = "";
		$search_field_exists = false;
		if(is_array($search_field)) {
			$search_field_exists = count($search_field)>0;
		} else {
			$search_field_exists = strlen($search_field)>0;
		
		}
		if (strlen($term)>0&&$search_field_exists) {
			$sWhere = "WHERE (";
			if(is_array($search_field)) {
				foreach($search_field as $f) {
					$sWhere .= "`".$f."` LIKE '%".mysql_real_escape_string( $term )."%' OR ";
				}
			} else {
				$sWhere .= "`".$search_field."` LIKE '%".mysql_real_escape_string( $term )."%' OR ";
			}
			
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		
		$qfilter = "select * from (".$q.") as a ".$sWhere.' '.$sOrder.' '.$sLimit;
		
		$r=$db->query($qfilter);
		
		$result = $r->result(false);
		$data = array();
		foreach ($r as $row) {
			$p = array();
			$p["id"]=$row[$key_field];
			foreach($row as $k=>$v) {
                                if($callback_function!=null) {
                                    $v = call_user_func($callback_function,$k,$row,$v);
                                }
				$p[$k]=$v;
			}
			//$p["id"]=$row["item_id"];
			$data[] = $p;
		}
		$result=array();
		$result["data"]=$data;
		$result["total"]=$total;
		
		$response = "";
		$response.= $callback."(";
		$response.= json_encode($result);
		$response.= ")";
		return $response;
		
	}
}