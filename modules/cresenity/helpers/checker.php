<?php

class checker {

    public static function data_is_existed($tbl = "", $data = array()) {
        $cont = true;
        $result = false;
        if (strlen(str_replace(" ", "", $tbl)) == 0)
            $cont = false;
        else if (count($data) == 0)
            $cont = false;

        if ($cont) {
            $db = CDatabase::instance();
            $qwhere = "";
            foreach ($data as $k => $v) {
                if ($qwhere == "")
                    $qwhere = $k . " = " . $db->escape($v);
                else
                    $qwhere .= " and " . $k . " = " . $db->escape($v);
            }
            $q = "select 1 from " . ($tbl) . " where " . $qwhere;
            $rs = $db->query($q);
            if (count($rs) > 0)
                $result = true;
            else
                $result = false;
        }
        return $result;
    }

    public static function is_empty($input) {
        $result = false;
        try {
            if (!isset($input)) {
                $result = true;
            } else {

                if (strlen(str_replace(" ", "", $input)) == 0) {
                    $result = true;
                }
            }
        } catch (CF_Exception $e) {
            $result = true;
        }
        return $result;
    }

}

?>
