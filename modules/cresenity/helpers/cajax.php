<?php

defined('SYSPATH') OR die('No direct access allowed.');

class cajax {

    public static function callback($obj, $input) {
        $callable = $obj->data->callable;
        $requires = cobj::get($obj->data, 'requires');
        if (!is_array($requires)) {
            $requires = array($requires);
        }

        foreach ($requires as $require) {
            if (strlen($require) > 0 && file_exists($require)) {
                require_once $require;
            }
        }
        if (is_callable($callable)) {
            return call_user_func($callable, $obj->data);
        }

        return false;
    }

    public static function form_process($obj, $input) {
        $db = CDatabase::instance();
        $form = $obj->data->form;
        $process_id = "";

        if (isset($input["ajax_process_id"])) {
            $process_id = $input["ajax_process_id"];
        }
        if (isset($input["ajax_process_id"])) {
            $last_process_id = cprogress::last_process_id();
        }
        if (isset($input["cancel"])) {
            $filename = $process_id . "_cancel" . ".tmp";
            $file = CTemporary::getPath("process", $filename);
            $disk = CTemporary::disk();


            $json = $disk->put($file, $form);
            echo json_encode(array(
                "result" => "0",
                "message" => "User cancelled",
            ));
            die();
        }

        $filename = $process_id . ".tmp";
        $file = CTemporary::getPath("process", $filename);
        $disk = CTemporary::disk();
        $json = '{"percent":0,"info":"Initializing"}';
        if ($disk->exists($file)) {
            $json = $disk->get($file);
        }
        echo $json;
    }

    public static function query($obj, $input) {
        $db = CDatabase::instance();
        $q = $obj->data->query;
        $param = $obj->data->param;

        foreach ($param as $p) {

            $val = $input[$p];
            $q = str_replace("{" . $p . "}", $db->escape($val), $q);
        }
        $r = $db->query($q);
        $data_list = $r->result(false);
        $data = array();
        foreach ($data_list as $row) {
            $arr = array();
            foreach ($row as $col => $val) {
                $arr[$col] = $val;
            }
            $data[] = $arr;
        }
        $response = json_encode($data);
        return $response;
    }

    public static function fillselect($obj, $input) {
        $db = CDatabase::instance();
        $q = $obj->query;
        $param = $obj->param;
        foreach ($param as $p) {

            $val = $input[$p->name];
            $q = str_replace("{" . $p->name . "}", $db->escape($val), $q);
        }
        $data_list = cdbutils::get_list($q);
        $response = json_encode($data_list);
        return $response;
    }

    public static function searchSelectElastic($obj, $input) {
        $callbackFunction = null;
        $callback = "";
        $term = "";
        $limit = "";
        $page = "";

        if (isset($input["callbackFunction"])) {
            $callbackFunction = $input["callbackFunction"];
        }
        if (isset($input["callback"])) {
            $callback = $input["callback"];
        }
        if (isset($input["term"])) {
            $term = $input["term"];
        }

        if (isset($input["limit"])) {
            $limit = $input["limit"];
        }
        if (isset($input["page"])) {
            $page = $input["page"];
        }
        $elasticIndex = $obj->elasticIndex;
        $elasticDocumentType = $obj->elasticDocumentType;
        $dataSearchable = $obj->dataSearchable;
        $dataSelect = $obj->dataSelect;
        $dataWhere = $obj->dataWhere;
        $dataCustomRow = $obj->dataCustomRow;
        $elastic = CElastic::instance();
        $search = $elastic->search($elasticIndex, $elasticDocumentType);
        foreach ($dataSelect as $key => $val) {
            $search->select($key, $val);
        }
        foreach ($dataWhere as $key => $detail) {
            if (is_array($detail)) {
                foreach ($detail as $key_d => $val_d) {
                    $search->$key($key_d, $val_d);
                }
            }
        }
        if (strlen($term) > 0) {
            if (strlen($dataSearchable) > 0) {
                $search->must('match.' . $dataSearchable, $term);
            }
        }
        if (strlen($limit) > 0) {
            $search->size($limit);
        }
        if (strlen($page) > 0) {
            $start = ($page - 1) * $limit;
            $search->from($start);
        }

        $elasticData = $search->exec();
        $key_field = $obj->data->key_field;
        $search_field = $obj->data->search_field;
        $data = array();
        $total = 0;
        foreach ($elasticData as $row) {
            $p = array();
            foreach ($row as $k => $v) {
                $v = ($v == null) ? "" : $v;
                if ($callbackFunction != null && is_callable($callbackFunction)) {
                    $v = call_user_func($callbackFunction, $row, $k, $v);
                }
                $p[$k] = $v;
            }
            $p["id"] = $row->$key_field;
            $data[] = $p;
        }
        if (count($dataCustomRow) > 0) {
            foreach ($dataCustomRow as $row) {
                $p = array();
                $temp = array();
                foreach ($row as $k => $v) {
                    $v = ($v == null) ? "" : $v;
                    $p[$k] = $v;
                }
                $p["id"] = $row[$key_field];
                $temp[] = $p;
                $data = array_merge($temp, $data);
            }
        }
        $result = array();
        $result["data"] = $data;
        $result["total"] = $total;

        $response = "";
        $response .= $callback . "(";
        $response .= json_encode($result);
        $response .= ")";
        return $response;
    }

    public static function searchselect($obj, $input) {
        if (isset($obj->is_elastic)) {
            return self::searchSelectElastic($obj, $input);
        }
        $db = CDatabase::instance();
        $q = $obj->data->query;
        $key_field = $obj->data->key_field;
        $search_field = $obj->data->search_field;
        $valueCallbackFunction = "";
        $callback = "";
        $term = "";
        $limit = "";
        $page = "";

        if (isset($input["valueCallback"])) {
            $callbackFunction = $input["valueCallback"];
        }
        if (isset($input["callback"])) {
            $callback = $input["callback"];
        }
        if (isset($input["term"])) {
            $term = $input["term"];
        }
        if (isset($input["q"])) {
            $term = $input["q"];
        }
        if (isset($input["limit"])) {
            $limit = $input["limit"];
        }
        if (isset($input["page"])) {
            $page = $input["page"];
        }
        $base_q = $q;
        $pos_order_by = strpos(strtolower($base_q), "order by", strpos(strtolower($base_q), 'from'));

        $pos_last_kurung = strrpos(strtolower($base_q), ")");
        if (isset($_GET['bdebug'])) {
            cdbg::var_dump($pos_order_by);
            cdbg::var_dump($pos_last_kurung);
            die();
        }
        $temp_order_by = '';
        if ($pos_order_by > $pos_last_kurung) {
            if ($pos_order_by !== false) {
                $temp_order_by = substr($base_q, $pos_order_by, strlen($base_q) - $pos_order_by);
                $base_q = substr($base_q, 0, $pos_order_by);
            }
        }

        $total = cdbutils::get_row_count_from_base_query($q);

        /* Paging */
        $sLimit = "";
        if (strlen($limit) > 0) {
            if (strlen($page) > 0) {
                $sLimit = "LIMIT " . ((intval($page) - 1) * 10) . ", " . intval($limit);
            } else {
                $sLimit = "LIMIT " . intval($limit);
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
        if (strlen($term) > 0 && (!empty($search_field))) {
            $sWhere = "WHERE (";
            if (is_array($search_field)) {
                foreach ($search_field as $f) {
                    $sWhere .= "`" . $f . "` LIKE '%" . $db->escape_like($term) . "%' OR ";
                }
            } else {
                $sWhere .= "`" . $search_field . "` LIKE '%" . $db->escape_like($term) . "%' OR ";
            }

            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';

            //order
            if (is_array($search_field)) {
                foreach ($search_field as $f) {
                    if (strlen($sOrder) > 0)
                        $sOrder .= ",";
                    $sOrder .= "`" . $f . "` = " . $db->escape($term) . " DESC";
                }
            }
        }

        if (strlen($sOrder) > 0) {
            $sOrder = " ORDER BY " . $sOrder;
            $temp_order_by = '';
        }

        if (strlen($temp_order_by) > 0) {
            $sub = explode(",", substr($temp_order_by, 9));
            $temp_order_by = "";
            foreach ($sub as $val) {
                $kata = explode(".", $val);
                if (isset($kata[1]))
                    $temp_order_by .= ", " . $kata[1];
                else
                    $temp_order_by .= ", " . $kata[0];
            }
            $temp_order_by = substr($temp_order_by, 2);
            $temp_order_by = "ORDER BY " . $temp_order_by;
        }

        $qfilter = "select * from (" . $base_q . ") as a " . $sWhere . ' ' . $sOrder;
        $total = cdbutils::get_row_count_from_base_query($qfilter);


        $qfilter .= " " . $temp_order_by . ' ' . $sLimit;

        $r = $db->query($qfilter);

        $result = $r->result(false);
        $data = array();

        foreach ($r as $row) {
            $p = array();
            foreach ($row as $k => $v) {
                $v = ($v == null) ? "" : $v;
                if ($valueCallbackFunction != null && is_callable($valueCallbackFunction)) {

                    $v = call_user_func($valueCallbackFunction, $row, $k, $v);
                }
                $p[$k] = $v;
            }
            $p["id"] = $row[$key_field];
            //$p["id"]=$row["item_id"];
            $data[] = $p;
        }
        $result = array();
        $result["data"] = $data;
        $result["total"] = $total;

        $response = "";
        $response .= $callback . "(";
        $response .= json_encode($result);
        $response .= ")";
        return $response;
    }

    public static function datatable($obj, $input) {

        $q = $obj->data->query;
        $param = $obj->param;
        $js = "";

        if ($obj->data->is_elastic) {
            return self::dataelastic($obj, $input);
        }

        foreach ($param as $p) {

            $val = $input[$p->name];
            $q = str_replace("{" . $p->name . "}", $db->escape($val), $q);
        }

        $table = unserialize($obj->data->table);
        //$db = CDatabase::instance($table->domain(),'ctable',$table->db_config);
        $domain = $obj->data->domain;
        $db = CDatabase::instance($domain);

        $request = $_GET;

        if (strtoupper($table->ajax_method) == "POST") {
            $request = $_POST;
        }
        $columns = $obj->data->columns;
        $row_action_list = $table->getRowActionList();
        $key = $obj->data->key_field;
        $base_q = $q;

        $max_where = strrpos(strtolower($base_q), 'where');
        $max_from = strrpos(strtolower($base_q), 'from');
        $max_offset = 0;
        if ($max_where !== false && $max_where > $max_offset) {
            $max_offset = $max_where;
        }
        if ($max_from !== false && $max_from > $max_offset) {
            $max_offset = $max_from;
        }

        $pos_order_by = strrpos(strtolower($base_q), "order by", $max_offset);

        $pos_last_kurung = strrpos(strtolower($base_q), ")");

        $temp_order_by = '';
        if ($pos_order_by > $pos_last_kurung) {
            if ($pos_order_by !== false) {
                $temp_order_by = substr($base_q, $pos_order_by, strlen($base_q) - $pos_order_by);
                $base_q = substr($base_q, 0, $pos_order_by);
            }
        }

        $qtotal = "select count(*) as cnt from (" . $q . ") as a";
        $rtotal = $db->query($qtotal);
        $total_record = 0;
        if ($rtotal->count() > 0)
            $total_record = $rtotal[0]->cnt;

        /* Paging */
        $sLimit = "";
        if (isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . intval($request['iDisplayStart']) . ", " . intval($request['iDisplayLength']);
        }


        /* Ordering */
        $sOrder = "";
        if (isset($request['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($request['iSortingCols']); $i++) {
                $i2 = 0;
                if ($table->checkbox) {
                    $i2 = -1;
                }
                if ($request['bSortable_' . intval($request['iSortCol_' . $i])] == "true") {
                    $sOrder .= "" . $db->escape_column($columns[intval($request['iSortCol_' . $i]) + $i2]->fieldname) . " " . $db->escape_str($request['sSortDir_' . $i]) . ", ";
                }
            }
            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        /**
         * Build condition query
         */
        $sWhere = "";
        $qs_condition_str = "";
        if (isset($request['sSearch']) && $request['sSearch'] != "") {
            for ($i = 0; $i < count($columns); $i++) {
                $i2 = 0;
                if ($table->checkbox) {
                    $i2 = -1;
                }
                if (isset($request['bSearchable_' . $i]) && $request['bSearchable_' . $i] == "true") {
                    $sWhere .= "`" . $columns[$i + $i2]->fieldname . "` LIKE '%" . $db->escape_like($request['sSearch']) . "%' OR ";
                }
            }
            $sWhere = substr_replace($sWhere, "", -3);
        }
        // Quick Search
        $qs_cond = array();
        if (isset($request['dttable_quick_search'])) {
            $qs_cond = json_decode($request['dttable_quick_search'], TRUE);
        }
        if (isset($qs_cond) && count($qs_cond) > 0) {


            foreach ($qs_cond as $qs_cond_k => $qs_cond_v) {
                $value = $qs_cond_v['value'];
                $transforms = carr::get($qs_cond_v, 'transforms');
                if (strlen($transforms) > 0) {
                    $transforms = json_decode($transforms, TRUE);

                    foreach ($transforms as $transforms_k => $transforms_v) {
                        $value = ctransform::$transforms_v['func']($value, TRUE);
                    }
                }

                $field_name = str_replace('dt_table_qs-', '', $qs_cond_v['name']);
                $qs_condition_str .= "`" . $field_name . "` LIKE '%" . $db->escape_like($value) . "%' AND ";
            }
            $qs_condition_str = substr_replace($qs_condition_str, "", -4);
            if (strlen(trim($sWhere)) > 0)
                $sWhere = ' ( ' . $sWhere . ' ) AND ';

            $sWhere .= $qs_condition_str;
        }

        if (strlen($sWhere) > 0) {
            $sWhere = " WHERE ( " . $sWhere . " )";
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($request['bSearchable_' . $i]) && $request['bSearchable_' . $i] == "true" && $request['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $sWhere .= "`" . $columns[$i]->fieldname . "` LIKE '%" . $db->escape_like($request['sSearch_' . $i]) . "%' ";
            }
        }

        $qfilter = "select * from (" . $base_q . ") as a " . $sWhere . ' ' . $sOrder;
        if (strlen($sOrder) > 0) {
            $temp_order_by = '';
        }
        $qtotal = "select count(*) as cnt from (" . $qfilter . ") as a";
        $rtotal = $db->query($qtotal);
        $filtered_record = 0;
        if ($rtotal->count() > 0)
            $filtered_record = $rtotal[0]->cnt;


        //die($temp_order_by);
        if (strlen($temp_order_by) > 0) {
            $sub = explode(",", substr($temp_order_by, 9));
            $temp_order_by = "";
            foreach ($sub as $val) {
                $kata = explode(".", $val);
                if (isset($kata[1]))
                    $temp_order_by .= ", " . $kata[1];
                else
                    $temp_order_by .= ", " . $kata[0];
            }
            $temp_order_by = substr($temp_order_by, 2);
            $temp_order_by = "ORDER BY " . $temp_order_by;
        }

        $qfilter .= " " . $temp_order_by . ' ' . $sLimit;
        $r = $db->query($qfilter);

        //$filtered_record = $r->count();
        $rarr = $r->result(false);
        $data = $rarr;
        $output = array(
            "sEcho" => intval(carr::get($request, 'sEcho')),
            "iTotalRecords" => $total_record,
            "iTotalDisplayRecords" => $filtered_record,
            "aaData" => array(),
        );
        $no = carr::get($request, 'iDisplayStart', 0);
        foreach ($data as $row) {
            $arr = array();
            $no++;
            $key = "";

            if (array_key_exists($table->key_field, $row)) {

                $key = $row[$table->key_field];
            }
            if ($table->numbering) {
                $arr[] = $no;
            }

            if ($table->checkbox) {
                $arr[] = '<input type="checkbox" name="' . $table->id() . '-check[]" id="' . $table->id() . '-' . $key . '" value="' . $key . '" class="checkbox-' . $table->id() . '">';
            }
            foreach ($table->columns as $col) {
                $col_found = false;
                $new_v = "";
                $col_v = "";
                $ori_v = "";
                //do print from query
                foreach ($row as $k => $v) {
                    if ($k == $col->get_fieldname()) {
                        $col_v = $v;
                        $ori_v = $col_v;
                        foreach ($col->transforms as $trans) {
                            $col_v = $trans->execute($col_v);
                        }
                    }
                }
                //if formatted
                if (strlen($col->format) > 0) {
                    $temp_v = $col->format;
                    foreach ($row as $k2 => $v2) {

                        if (strpos($temp_v, "{" . $k2 . "}") !== false) {

                            $temp_v = str_replace("{" . $k2 . "}", $v2, $temp_v);
                        }
                        $col_v = $temp_v;
                    }
                }

                $new_v = $col_v;

                if (($table->cell_callback_func) != null) {
                    $new_v = CDynFunction::factory($table->cell_callback_func)
                            ->add_param($table)
                            ->add_param($col->get_fieldname())
                            ->add_param($row)
                            ->add_param($new_v)
                            ->set_require($table->requires)
                            ->execute();

                    if (is_array($new_v) && isset($new_v['html']) && isset($new_v['js'])) {
                        $js .= $new_v['js'];
                        $new_v = $new_v['html'];
                    }
                }
                $class = "";
                switch ($col->get_align()) {
                    case "left": $class .= " align-left";
                        break;
                    case "right": $class .= " align-right";
                        break;
                    case "center": $class .= " align-center";
                        break;
                }
                $arr[] = $new_v;
            }
            if ($row_action_list != null) {
                $html = new CStringBuilder();
                $html->appendln('<td class="low-padding align-center cell-action td-action">')->inc_indent()->br();
                foreach ($row as $k => $v) {
                    $jsparam[$k] = $v;
                }
                $jsparam["param1"] = $key;
                if ($table->getRowActionStyle() == "btn-dropdown") {
                    $table->getRowActionList()->add_class("pull-right");
                }
                $row_action_list->regenerateId(true);
                $row_action_list->apply("jsparam", $jsparam);

                $row_action_list->apply("set_handler_url_param", $jsparam);

                if (($table->filter_action_callback_func) != null) {
                    $actions = $row_action_list->childs();

                    foreach ($actions as $action) {
                        $visibility = CDynFunction::factory($table->filter_action_callback_func)
                                ->add_param($table)
                                ->add_param($col->get_fieldname())
                                ->add_param($row)
                                ->add_param($action)
                                ->set_require($table->requires)
                                ->execute();

                        $action->set_visibility($visibility);
                    }
                }

                $html->appendln($row_action_list->html($html->get_indent()));
                $js .= $row_action_list->js();
                $html->dec_indent()->appendln('</td>')->br();
                //$arr[] = '';
                $arr[] = $html->text();
                $arr["DT_RowId"] = $key;
            }
            $output["aaData"][] = $arr;
        }


        $data = array(
            "datatable" => $output,
            "js" => cbase64::encode($js),
        );
        $response = json_encode($data);
        return $response;
    }

    public static function handler_reload($obj, $input) {

        $data = $obj->data->json;

        return $data;
    }

    public static function imgupload($obj, $input) {
        $return = array();
        $data = $obj->data;
        $input_name = $data->input_name;
        $fileId = '';
        if (isset($_FILES[$input_name]) && isset($_FILES[$input_name]['name'])) {
            for ($i = 0; $i < count($_FILES[$input_name]['name']); $i++) {
                $extension = "." . pathinfo($_FILES[$input_name]['name'][$i], PATHINFO_EXTENSION);
                if (strtolower($extension) == 'php') {
                    die('fatal error');
                }
                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                $fullfilename = CTemporary::getPath("imgupload", $fileId);
                $disk = CTemporary::disk();
                if (!$disk->put($fullfilename, file_get_contents($_FILES[$input_name]['tmp_name'][$i]))) {
                    die('fail upload from ' . $_FILES[$input_name]['tmp_name'][$i] . ' to ' . $fullfilename);
                }
                $return[] = $fileId;
            }
        }

        if (isset($_POST[$input_name])) {

            $imageDataArray = $_POST[$input_name];
            $filenameArray = $_POST[$input_name . '_filename'];

            if (!is_array($imageDataArray)) {
                $imageDataArray = array($imageDataArray);
            }
            if (!is_array($filenameArray)) {
                $filenameArray = array($filenameArray);
            }
            foreach ($imageDataArray as $k => $imageData) {
                $filename = carr::get($filenameArray, $k);
                $extension = "." . pathinfo($filename, PATHINFO_EXTENSION);
                if (strtolower($extension) == 'php') {
                    die('fatal error');
                }

                $filteredData = substr($imageData, strpos($imageData, ",") + 1);
                $unencodedData = base64_decode($filteredData);
                $fileId = date('Ymd') . cutils::randmd5() . $extension;
                $fullfilename = CTemporary::getPath("imgupload", $fileId);
                $disk = CTemporary::disk();
                $disk->put($fullfilename, $unencodedData);
                $return[] = $fileId;
            }
        }
        $return = array(
            'file_id' => $fileId,
            'url' => CTemporary::getUrl('imgupload', $fileId),
        );
        return json_encode($return);
    }

    public static function fileupload($obj, $input) {
        $return = array();
        $data = $obj->data;
        $input_name = $data->input_name;

        if (isset($_FILES[$input_name]) && isset($_FILES[$input_name]['name'])) {
            for ($i = 0; $i < count($_FILES[$input_name]['name']); $i++) {
                $extension = "." . pathinfo($_FILES[$input_name]['name'][$i], PATHINFO_EXTENSION);
                $file_id = date('Ymd') . cutils::randmd5() . $extension;

                $fullfilename = CTemporary::getPath("fileupload", $file_id . ".tmp");
                $disk = CTemporary::disk();
                $disk->put($fullfilename, file_get_contents($_FILES[$input_name]['tmp_name'][$i]));
                $return[] = $file_id;
            }
        }
        return $file_id;
        //$response = json_encode($return);
        //return $response;
    }

    public static function dataelastic($obj, $input) {
        $ajax_data = $obj->data->query;
        $param = $obj->param;
        $js = "";

        foreach ($param as $p) {

            $val = $input[$p->name];
            $q = str_replace("{" . $p->name . "}", $db->escape($val), $q);
        }

        $table = unserialize($obj->data->table);
        //$db = CDatabase::instance($table->domain(),'ctable',$table->db_config);
        $domain = $obj->data->domain;

        /*
         * @var CElastic
         */
        $el = CElastic::instance();

        $request = $_GET;

        if (strtoupper($table->ajax_method) == "POST") {
            $request = $_POST;
        }

        $columns = $obj->data->columns;
        $row_action_list = $table->getRowActionList();
        $key = $obj->data->key_field;

        $elastic_index = cobj::get($ajax_data, 'index');
        $elastic_document_type = cobj::get($ajax_data, 'document_type');


        $search = $el->search($elastic_index, $elastic_document_type);

        $mapping = $search->indices()->get_mapping();
        $properties = carr::path($mapping, $elastic_index . '.mappings.' . $elastic_document_type . '.properties');


        $must = cobj::get($ajax_data, 'must');
        foreach ($must as $m) {
            $search->must((array) $m);
        }
        $must_not = cobj::get($ajax_data, 'must_not');
        foreach ($must_not as $mn) {
            $search->must_not((array) $mn);
        }

        $select_raw = (array) cobj::get($ajax_data, 'select');

        foreach ($select_raw as $k => $v) {
            $v = (array) $v;

            $select[carr::get($v, 'field')] = carr::get($v, 'alias');
            $select_flip[carr::get($v, 'alias')] = carr::get($v, 'field');
        }



        foreach ($select_raw as $k => $v) {
            $v = (array) $v;

            $search->select(carr::get($v, 'field'), carr::get($v, 'alias'));
        }

        $sort = (array) cobj::get($ajax_data, 'sort', array());
        $from = cobj::get($ajax_data, 'from', 0);
        $size = cobj::get($ajax_data, 'size', 10);



        /* Paging */
        if (isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1') {
            $search->from(intval($request['iDisplayStart']));
            $search->size(intval($request['iDisplayLength']));
        }


        /* Ordering */
        if (isset($request['iSortCol_0'])) {
            for ($i = 0; $i < intval($request['iSortingCols']); $i++) {
                $i2 = 0;
                if ($table->checkbox) {
                    $i2 = -1;
                }
                if ($request['bSortable_' . intval($request['iSortCol_' . $i])] == "true") {

                    $field = $columns[intval($request['iSortCol_' . $i]) + $i2]->fieldname;

                    $sort_mode = $request['sSortDir_' . $i];
                    if (strlen($field) > 0) {
                        if (isset($select_flip[$field])) {
                            $field = $select_flip[$field];
                        }

                        $search->sort($field, $sort_mode);
                    }
                }
            }
        } else {
            foreach ($sort as $s) {
                $search->sort($s);
            }
        }

        if (isset($request['sSearch']) && $request['sSearch'] != "") {
            $arr = array();
            if (count($columns) > 0) {
                carr::set_path($arr, 'bool.should', array());
                $should = &$arr['bool']['should'];
                for ($i = 0; $i < count($columns); $i++) {
                    $i2 = 0;
                    if ($table->checkbox) {
                        $i2 = -1;
                    }
                    if (isset($request['bSearchable_' . $i]) && $request['bSearchable_' . $i] == "true") {
                        $field = $columns[$i + $i2]->fieldname;
                        if (isset($select_flip[$field])) {
                            $field = $select_flip[$field];
                        }

                        $s = array();
                        $fieldElastic = $search->getElasticField($field);
                        $elastic_field_type = carr::path($properties, $fieldElastic . '.type');

                        switch ($elastic_field_type) {
                            case 'text':
                                carr::set_path($s, 'match.' . $fieldElastic, $request['sSearch']);

                                break;
                            case 'date':
                                //do nothing
                                break;
                            case 'long':
                            case 'float':
                            default:
                                if (is_numeric($request['sSearch'])) {
                                    carr::set_path($s, 'term.' . $fieldElastic, $request['sSearch']);
                                }
                                break;
                        }
                        if (count($s) > 0) {
                            $should[] = $s;
                        }
                    }
                }
            }
            if (count($arr) > 0) {

                $search->must($arr);
            }
        }

        if (isset($_GET['debug'])) {
            cdbg::var_dump($search->buildParams());
            die;
        }

        $r = $search->exec();


        $total_record = $r->count_all();
        $filtered_record = min($r->count_all(), 10000);


        $rarr = $r->result(false);
        $data = $rarr;
        $output = array(
            "sEcho" => intval(carr::get($request, 'sEcho')),
            "iTotalRecords" => $total_record,
            "iTotalDisplayRecords" => $filtered_record,
            "aaData" => array(),
        );
        $no = carr::get($request, 'iDisplayStart', 0);
        foreach ($data as $row) {
            $arr = array();
            $no++;
            $key = "";

            if (array_key_exists($table->key_field, $row)) {

                $key = $row[$table->key_field];
            }
            if ($table->numbering) {
                $arr[] = $no;
            }

            if ($table->checkbox) {
                $arr[] = '<input type="checkbox" name="' . $table->id() . '-check[]" id="' . $table->id() . '-' . $key . '" value="' . $key . '" class="checkbox-' . $table->id() . '">';
            }
            foreach ($table->columns as $col) {
                $col_found = false;
                $new_v = "";
                $col_v = "";
                $ori_v = "";
                //do print from query
                foreach ($row as $k => $v) {
                    if ($k == $col->get_fieldname()) {
                        $col_v = $v;
                        $ori_v = $col_v;
                        foreach ($col->transforms as $trans) {
                            $col_v = $trans->execute($col_v);
                        }
                    }
                }
                //if formatted
                if (strlen($col->format) > 0) {
                    $temp_v = $col->format;
                    foreach ($row as $k2 => $v2) {

                        if (strpos($temp_v, "{" . $k2 . "}") !== false) {

                            $temp_v = str_replace("{" . $k2 . "}", $v2, $temp_v);
                        }
                        $col_v = $temp_v;
                    }
                }

                $new_v = $col_v;

                if (($table->cell_callback_func) != null) {
                    $new_v = CDynFunction::factory($table->cell_callback_func)
                            ->add_param($table)
                            ->add_param($col->get_fieldname())
                            ->add_param($row)
                            ->add_param($new_v)
                            ->set_require($table->requires)
                            ->execute();

                    if (is_array($new_v) && isset($new_v['html']) && isset($new_v['js'])) {
                        $js .= $new_v['js'];
                        $new_v = $new_v['html'];
                    }


                    //call_user_func($this->cell_callback_func,$this,$col->get_fieldname(),$row,$v);
                }
                $class = "";
                switch ($col->get_align()) {
                    case "left": $class .= " align-left";
                        break;
                    case "right": $class .= " align-right";
                        break;
                    case "center": $class .= " align-center";
                        break;
                }
                $arr[] = $new_v;
            }
            if (count($row_action_list) > 0) {
                $html = new CStringBuilder();
                ;
                $html->appendln('<td class="low-padding align-center cell-action td-action">')->inc_indent()->br();
                foreach ($row as $k => $v) {
                    $jsparam[$k] = $v;
                }
                $jsparam["param1"] = $key;
                if ($table->getRowActionStyle() == "btn-dropdown") {
                    $table->getRowActionList()->add_class("pull-right");
                }

                $row_action_list->regenerateId(true);
                $row_action_list->apply("jsparam", $jsparam);

                $row_action_list->apply("set_handler_url_param", $jsparam);

                if (($table->filter_action_callback_func) != null) {
                    $actions = $row_action_list->childs();

                    foreach ($actions as $action) {
                        $visibility = CDynFunction::factory($table->filter_action_callback_func)
                                ->add_param($table)
                                ->add_param($col->get_fieldname())
                                ->add_param($row)
                                ->add_param($action)
                                ->set_require($table->requires)
                                ->execute();

                        $action->set_visibility($visibility);
                    }


                    //call_user_func($this->cell_callback_func,$this,$col->get_fieldname(),$row,$v);
                }

                $html->appendln($row_action_list->html($html->get_indent()));
                $js .= $row_action_list->js();
                $html->dec_indent()->appendln('</td>')->br();
                //$arr[] = '';
                $arr[] = $html->text();
                $arr["DT_RowId"] = $key;
            }
            $output["aaData"][] = $arr;
        }


        $data = array(
            "datatable" => $output,
            "js" => cbase64::encode($js),
        );
        $response = json_encode($data);
        return $response;
    }

}
