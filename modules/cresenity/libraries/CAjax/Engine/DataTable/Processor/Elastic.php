<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 2:58:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_DataTable_Processor_Elastic extends CAjax_Engine_DataTable_Processor {

    public function process() {
        $data = $this->data;
        $ajaxData = carr::get($data, 'query');

        $js = "";


        $table = unserialize(carr::get($data, 'table'));
        //$db = CDatabase::instance($table->domain(),'ctable',$table->db_config);
        $domain = carr::get($data, 'domain');
        $ajaxDataDomain = carr::get($ajaxData, 'domain');
        if(strlen($ajaxDataDomain)>0) {
            $domain = $ajaxDataDomain;
        }
        $instanceName = carr::get($ajaxData,'name');
        $instanceConfig = carr::get($ajaxData,'config');
        
        /*
         * @var CElastic
         */
        $el = CElastic::instance($domain,$instanceName,$instanceConfig);

        $request = $this->input;


        $columns = carr::get($data, 'columns');
        $row_action_list = $table->getRowActionList();
        $key = carr::get($data, 'key_field');

        $elastic_index = carr::get($ajaxData, 'index');
        $elastic_document_type = carr::get($ajaxData, 'document_type');
        

        $search = $el->search($elastic_index, $elastic_document_type);
        $mapping = $search->indices()->get_mapping();
        $properties = carr::path($mapping, $elastic_index . '.mappings.' . $elastic_document_type . '.properties');


        $must = carr::get($ajaxData, 'must');
        foreach ($must as $m) {
            $search->must((array) $m);
        }
        $must_not = carr::get($ajaxData, 'must_not');
        foreach ($must_not as $mn) {
            $search->must_not((array) $mn);
        }

        $select_raw = (array) carr::get($ajaxData, 'select');

        foreach ($select_raw as $k => $v) {
            $v = (array) $v;

            $select[carr::get($v, 'field')] = carr::get($v, 'alias');
            $select_flip[carr::get($v, 'alias')] = carr::get($v, 'field');
        }



        foreach ($select_raw as $k => $v) {
            $v = (array) $v;

            $search->select(carr::get($v, 'field'), carr::get($v, 'alias'));
        }

        $sort = (array) carr::get($ajaxData, 'sort', array());
        $from = carr::get($ajaxData, 'from', 0);
        $size = carr::get($ajaxData, 'size', 10);



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
            cdbg::dd($search->buildParams());
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
