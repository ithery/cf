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
        if (isset($data->org_code))
            $data->code = $data->org_code;
        if (isset($data->org_code))
            $data->name = $data->org_name;

        return $data;
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

}
