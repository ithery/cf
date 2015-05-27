<?php

class corg {

    public static function get($id) {
        $default_data = array(
            "org_id" => "",
            "org_code" => "",
            "org_name" => "",
            "abbr" => "",
        );
		
        $data = cdata::get($id, 'org');
        if ($data == null)
            return null;
        foreach ($default_data as $k => $v) {
            
			if (!isset($data[$k])) {
                $data[$k] = $v;
            }
        }
        //$data = array_merge($default_data,$data);

        if ($data != null) {
            $data = carr::to_object($data);
        }
        return $data;

        /*
          $db = CJDB::instance();
          $result = $db->get("org",array("org_id"=>$id));

          $value = null;
          if ($result->count() > 0)
          $value = $result[0];
          return $value;
         */
    }

    public static function data($org_id, $data = null) {
        $org = corg::get($org_id);
        $org_data = cdata::get($org->code, 'org_data');
        if (!is_array($org_data))
            $org_data = array();
        if (is_array($data)) {
            $org_data = array_merge($org_data, $data);
            cdata::set($org->code, $org_data, 'org_data');
        }
        return $org_data;
    }

    public static function get_stores($org_id) {
        $db = CJDB::instance();
        $result = $db->get("store", array("org_id" => $org_id));

        return $result;
    }

}
