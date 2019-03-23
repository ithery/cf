<?php

class CElement_Component_DataTable extends CElement_Component {

    use CTrait_Compat_Element_DataTable,
        CTrait_Element_ActionList_Row,
        CTrait_Element_ActionList_Header,
        CElement_Component_DataTable_Trait_ExportTrait;

    public $defaultPagingList = array(
        "10" => "10",
        "25" => "25",
        "50" => "50",
        "100" => "100",
        "-1" => "ALL",
    );
    public $current_row = 1;

    /**
     *
     * @var CDatabase
     */
    public $db;
    public $dbConfig;
    public $columns;
    public $footer;
    public $footer_field;
    public $requires = array();
    public $data;
    public $key_field;
    public $checkbox;
    public $checkbox_value;
    public $numbering;
    public $query;
    public $custom_column_header;
    public $header_sortable;
    public $cell_callback_func;
    public $filter_action_callback_func;
    public $display_length;
    public $paging_list;
    public $responsive;
    public $options;
    public $apply_data_table;
    public $group_by;
    public $title;
    public $ajax;
    public $ajax_method;
    public $icon;
    public $editable_form;
    public $can_edit;
    public $can_add;
    public $can_delete;
    public $can_view;
    public $headerNoLineBreak;
    public $pdf_font_size;
    public $pdf_orientation;
    public $show_header;
    public $footer_action_list = array();
    public $footer_action_style;
    public $isElastic = false;
    public $isCallback = false;
    public $callbackRequire = null;
    public $callbackOptions = null;
    protected $tableStriped;
    protected $tableBordered;
    protected $quick_search = FALSE;
    protected $tbody_id;
    protected $js_cell;
    protected $dom = null;
    protected $widget_title;
    protected $haveColRowView;

    public function __construct($id = "") {
        parent::__construct($id);
        $this->defaultPagingList["-1"] = clang::__("ALL");
        $this->tag = "table";
        $this->responsive = false;
        $this->db = CDatabase::instance($this->domain);
        $this->dbConfig = $this->db->config();
        $this->display_length = "10";
        $this->paging_list = $this->defaultPagingList;
        $this->options = CElement_Component_DataTable_Options::factory();
        $this->data = array();
        $this->key_field = "";
        $this->columns = array();
        $this->rowActionList = CElement_Factory::createList('ActionList');
        $this->rowActionList->setStyle('btn-icon-group');
        $this->headerActionList = CElement_Factory::createList('ActionList');
        $this->headerActionList->setStyle('widget-action');
        $this->footer_action_list = CElement_List_ActionList::factory();
        $this->footer_action_style = 'btn-list';
        $this->footer_action_list->set_style('btn-list');
        $this->checkbox = false;
        $this->checkbox_value = array();
        $this->numbering = false;
        $this->query = '';
        $this->header_sortable = true;
        $this->footer = false;
        $this->footer_field = array();
        $this->cell_callback_func = "";
        $this->filter_action_callback_func = "";
        $this->display_length = "10";
        $this->ajax = false;
        $this->ajax_method = "get";
        $this->title = "";
        $this->editable_form = null;
        $this->can_edit = false;
        $this->can_add = false;
        $this->can_delete = false;
        $this->can_view = false;
        $this->export_pdf = false;
        $this->export_excelxml = false;
        $this->export_excelcsv = false;
        $this->export_xml = false;
        $this->export_excel = false;
        $this->headerNoLineBreak = false;

        $this->custom_column_header = "";
        $this->show_header = true;
        $this->apply_data_table = true;
        $this->group_by = "";
        $this->icon = "";
        $this->pdf_font_size = 8;
        $this->pdf_orientation = 'P';
        $this->requires = array();
        $this->js_cell = '';
        $this->quick_search = FALSE;
        $this->tbody_id = '';

        $this->report_header = array();


        $this->widget_title = true;

        //$this->add_footer_action($this->id.'_export_excel');

        $this->export_filename = $this->id;
        $this->export_sheetname = $this->id;
        $this->tableStriped = true;
        $this->tableBordered = true;
        $this->haveColRowView = true;

        if (isset($this->theme)) {
            if ($this->bootstrap >= '3.3') {
                CClientModules::instance()->register_module('jquery.datatable-bootstrap3');
            } else {
                CClientModules::instance()->register_module('jquery.datatable');
            }
        } else {
            CClientModules::instance()->register_module('jquery.datatable');
        }


        $this->dom = CManager::theme()->getData('table.dom');
    }

    public static function factory($id = "") {
        return new CElement_Component_DataTable($id);
    }

    public function setDomain($domain) {
        parent::setDomain($domain);
        $this->setDatabase(CDatabase::instance($domain));
        return $this;
    }

    public function setDatabase($db) {
        $this->db = $db;
        $this->dbConfig = $db->config();

        return $this;
    }

    function setTableStriped($tableStriped) {
        $this->tableStriped = $tableStriped;
        return $this;
    }

    function setTableBordered($bool) {
        $this->tableBordered = $bool;
        return $this;
    }

    public function setWidgetTitle($bool) {
        $this->widget_title = $bool;
        return $this;
    }

    public static function actionDownloadExcel($data) {
        $table = $data->table;
        $table = unserialize($table);
        $export_filename = $table->export_filename;
        if (substr($table->export_filename, 3) != 'xls') {
            $export_filename .= '.xls';
        }
        self::exportExcelxmlStatic($export_filename, $table->export_sheetname, $table);
    }

    public function addFooterAction($id = "") {
        $row_act = CAction::factory($id);
        $this->footer_action_list->add($row_act);

        return $row_act;
    }

    public function haveFooterAction() {
        //return $this->can_edit||$this->can_delete||$this->can_view;
        return $this->footer_action_list->child_count() > 0;
    }

    public function setTitle($title, $lang = true) {
        if ($lang) {
            $title = clang::__($title);
        }
        $this->title = $title;
        return $this;
    }

    public function setDom($dom) {
        $this->dom = $dom;
        return $this;
    }

    public function setCustomColumnHeader($html) {
        $this->custom_column_header = $html;
        return $this;
    }

    public function setFooter($bool) {
        $this->footer = $bool;
        return $this;
    }

    public function setResponsive($bool) {
        $this->responsive = $bool;
        return $this;
    }

    public function setShowHeader($bool) {
        $this->show_header = $bool;
        return $this;
    }

    public function setQuickSearch($quick_search) {
        $this->quick_search = $quick_search;
        return $this;
    }

    public function setTbodyId($id) {
        $this->tbody_id = $id;
        return $this;
    }

    public function addFooterField($label, $value, $align = "left", $labelcolspan = 0) {
        $f = array(
            "label" => $label,
            "value" => $value,
            "align" => $align,
            "labelcolspan" => $labelcolspan,
        );
        $this->footer_field[] = $f;
        return $this;
    }

    public function setHeaderNoLineBreak($bool) {
        $this->headerNoLineBreak = $bool;
        return $this;
    }

    public function setOption($key, $val) {
        $this->options->add($key, $val);
        return $this;
    }

    public function getOption($key) {
        return $this->options->get_by_name($key);
    }

    public function setAjax($bool = true) {
        $this->ajax = $bool;
        return $this;
    }

    public function setAjaxMethod($value) {
        $this->ajax_method = $value;
        return $this;
    }

    /**
     * 
     * @param bool $bool
     * @return CElement_Component_DataTable
     */
    public function setApplyDataTable($bool) {
        $this->apply_data_table = $bool;
        return $this;
    }

    public function setDisplayLength($length) {
        $this->display_length = $length;
        return $this;
    }

    /**
     * Set callback for table cell render
     * 
     * @param callable $func parameter: $table,$col,$row,$value 
     * @param string $require File location of callable function to require
     * @return $this
     */
    public function cellCallbackFunc($func, $require = "") {
        $this->cell_callback_func = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function filterActionCallbackFunc($func, $require = "") {
        $this->filter_action_callback_func = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function setKey($fieldname) {
        $this->key_field = $fieldname;
        return $this;
    }

    /**
     * 
     * @param string $fieldname
     * @return CElement_Component_DataTable_Column
     */
    public function addColumn($fieldname) {
        $col = CElement_Component_DataTable_Column::factory($fieldname);
        $this->columns[] = $col;
        return $col;
    }

    /**
     * 
     * @param string $group_by
     * @return $this
     */
    public function setGroupBy($group_by) {
        $this->group_by = $group_by;
        return $this;
    }

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setCheckbox($bool) {
        $this->checkbox = $bool;
        return $this;
    }

    /**
     * 
     * @param string $val
     * @return $this
     */
    public function setCheckboxValue($val) {
        if (!is_array($val)) {
            $val = array($val);
        }
        $this->checkbox_value = $val;
        return $this;
    }

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setHeaderSortable($bool = true) {
        $this->header_sortable = $bool;
        return $this;
    }

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setNumbering($bool = true) {
        $this->numbering = $bool;
        return $this;
    }

    /**
     * 
     * alias for setNumbering(true)
     * @return $this
     */
    public function enableNumbering() {
        $this->numbering = true;
        return $this;
    }

    /**
     * alias for setNumbering(false)
     * @return $this
     */
    public function disableNumbering() {
        $this->numbering = false;
        return $this;
    }

    public function enableCheckbox() {
        $this->checkbox = true;
        return $this;
    }

    public function disableCheckbox() {
        $this->checkbox = false;
        return $this;
    }

    public function setQuery($q) {
        $this->query = $q;
        return $this;
    }

    /**
     * 
     * @param string $q
     * @return $this
     */
    public function setDataFromQuery($q) {
        if ($this->ajax == false) {
            $db = $this->db;
            $r = $db->query($q);
            $this->data = $r->result(false);
        }
        $this->query = $q;
        return $this;
    }

    /**
     * 
     * @param CModel|CModel_Query $model
     * @return $this
     */
    public function setDataFromModel($model) {
        $modelQuery = $model;
        if ($modelQuery instanceof CModel_Collection) {
            throw new CException('error when calling setDataFromModel, please use CModel/CModel_Query instance (CModel_Collection passed)');
        }
        $sql = $this->db->compileBinds($modelQuery->toSql(), $modelQuery->getBindings());
        return $this->setDataFromQuery($sql);
    }

    /**
     * 
     * @param CElastic_Search $el
     * @param string $require
     * @return $this
     */
    public function setDataFromElastic($el, $require = null) {
        $this->query = $el;
        $this->isElastic = true;
        if ($el instanceof CElastic_Search) {

            $this->query = $el->ajax_data();
        }
        return $this;
    }

    /**
     * 
     * @param callable $callback
     * @param array $callbackOptions
     * @param string $require
     * @return $this
     */
    public function setDataFromCallback($callback, $callbackOptions = array(), $require = null) {
        $this->query = $callback;
        $this->isCallback = true;
        $this->callbackOptions = $callbackOptions;
        $this->callbackRequire = $require;

        return $this;
    }

    /**
     * 
     * @param array $a
     * @return $this
     */
    public function setDataFromArray($arr) {
        $this->data = $arr;
        return $this;
    }

    public function rawHtml($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);

        $thClass = "";
        if ($this->headerNoLineBreak) {
            $thClass = " no-line-break";
        }
        $htmlResponsiveOpen = '<div class="table-responsive">';
        $htmlResponsiveClose = '</div>';
        if ($this->responsive) {
            $htmlResponsiveOpen = '<div class="span12" style="overflow: auto;margin-left: 0;">';
            $htmlResponsiveClose = '</div>';
        }

        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0) {
            $classes = " " . $classes;
        }
        if ($this->tableStriped) {
            $classes .= " table-striped ";
        }
        if ($this->tableBordered) {
            $classes .= " table-bordered ";
        }

        $html->appendln($htmlResponsiveOpen . '<table ' . $this->getPdfTableAttr() . ' class="table responsive ' . $classes . '" id="' . $this->id . '">')
                ->incIndent()->br();
        if ($this->show_header) {
            $html->appendln('<thead>')
                    ->incIndent()->br();
            if (strlen($this->custom_column_header) > 0) {
                $html->appendln($this->custom_column_header);
            } else {
                $html->appendln('<tr>')
                        ->incIndent()->br();

                if ($this->numbering) {
                    $html->appendln('<th data-align="align-right" class="' . $thClass . '" width="20" scope="col">No</th>')->br();
                }
                if ($this->checkbox) {
                    $html->appendln('<th class="align-center" data-align="align-center" class="' . $thClass . '" scope="col"><input type="checkbox" name="' . $this->id . '-check-all" id="' . $this->id . '-check-all" value="1"></th>')->br();
                }
                foreach ($this->columns as $col) {
                    $html->appendln($col->render_header_html($this->export_pdf, $thClass, $html->getIndent()))->br();
                }
                if ($this->haveRowAction()) {
                    $action_width = 31 * $this->action_count() + 5;
                    if ($this->getRowActionStyle() == "btn-dropdown") {
                        $action_width = 70;
                    }
                    $html->appendln('<th data-action="cell-action td-action" data-align="align-center" scope="col" width="' . $action_width . '" class="align-center cell-action th-action' . $thClass . '">' . clang::__('Actions') . '</th>')->br();
                }
                $html->decIndent()->appendln("</tr>")->br();
            }
            $html->decIndent()->appendln("</thead>")->br();
        }

        $tbody_id = (strlen($this->tbody_id) > 0 ? "id='" . $this->tbody_id . "' " : "");

        $html->appendln("<tbody " . $tbody_id . " >")->incIndent()->br();
        //render body;
        $html->appendln($this->htmlChild($indent));
        $no = 0;
        if (!$this->ajax) {
            foreach ($this->data as $row) {
                if ($row instanceof CRenderable) {
                    $html->appendln($row->html());
                    continue;
                }

                $no++;
                $key = "";

                if (array_key_exists($this->key_field, $row)) {

                    $key = $row[$this->key_field];
                }
                $html->appendln('<tr id="tr-' . $key . '">')->incIndent()->br();

                if ($this->numbering) {
                    $html->appendln('<td scope="row" class="align-right">' . $no . '</td>')->br();
                }
                if ($this->checkbox) {
                    $checkbox_checked = "";
                    if (in_array($key, $this->checkbox_value)) {
                        $checkbox_checked = ' checked="checked"';
                    }
                    $html->appendln('<td scope="row" class="checkbox-cell align-center"><input type="checkbox" class="checkbox-' . $this->id . '" name="' . $this->id . '-check[]" id="' . $this->id . '-' . $key . '" value="' . $key . '"' . $checkbox_checked . '></td>')->br();
                }
                $jsparam = array();
                foreach ($this->columns as $col) {
                    $col_found = false;
                    $new_v = "";
                    $col_v = "";
                    $ori_v = "";
                    //do print from query
                    foreach ($row as $k => $v) {
                        if ($v instanceof CRenderable) {
                            $v = $v->html();
                        }
                        if ($k == $col->getFieldname()) {
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

                    if (($this->cell_callback_func) != null) {
                        $new_v = CFunction::factory($this->cell_callback_func)
                                ->addArg($this)
                                ->addArg($col->getFieldname())
                                ->addArg($row)
                                ->addArg($new_v)
                                ->setRequire($this->requires)
                                ->execute();


                        //call_user_func($this->cell_callback_func,$this,$col->get_fieldname(),$row,$v);
                    }
                    $class = "";
                    switch ($col->getAlign()) {
                        case CConstant::ALIGN_LEFT:
                            $class .= " align-left";
                            break;
                        case CConstant::ALIGN_RIGHT:
                            $class .= " align-right";
                            break;
                        case CConstant::ALIGN_CENTER:
                            $class .= " align-center";
                            break;
                    }
                    if ($col->getNoLineBreak()) {
                        $class .= " no-line-break";
                    }
                    if ($col->hidden_phone)
                        $class .= " hidden-phone";

                    if ($col->hidden_tablet)
                        $class .= " hidden-tablet";

                    if ($col->hidden_desktop)
                        $class .= " hidden-desktop";

                    $pdf_tbody_td_current_attr = $this->getPdfTBodyTdAttr();
                    if ($this->export_pdf) {
                        switch ($col->get_align()) {
                            case "left": $pdf_tbody_td_current_attr .= ' align="left"';
                                break;
                            case "right": $pdf_tbody_td_current_attr .= ' align="right"';
                                break;
                            case "center": $pdf_tbody_td_current_attr .= ' align="center"';
                                break;
                        }
                    }
                    if (is_array($new_v)) {
                        $this->js_cell .= carr::get($new_v, 'js', '');
                        $new_v = carr::get($new_v, 'html', '');
                    }

                    $html->appendln('<td' . $pdf_tbody_td_current_attr . ' class="' . $class . '" data-column="' . $col->getFieldname() . '">' . $new_v . '</td>')->br();
                    $col_found = true;
                }

                if ($this->haveRowAction()) {
                    $html->appendln('<td class="low-padding align-center cell-action td-action">')->incIndent()->br();
                    foreach ($row as $k => $v) {
                        $jsparam[$k] = $v;
                    }

                    $jsparam["param1"] = $key;
                    if ($this->getRowActionStyle() == "btn-dropdown") {
                        $this->rowActionList->add_class("pull-right");
                    }
                    $this->rowActionList->regenerateId(true);
                    $this->rowActionList->apply("jsparam", $jsparam);
                    $this->rowActionList->apply("set_handler_url_param", $jsparam);

                    if (($this->filter_action_callback_func) != null) {
                        $actions = $this->rowActionList->childs();

                        foreach ($actions as &$action) {
                            $visibility = CDynFunction::factory($this->filter_action_callback_func)
                                    ->add_param($this)
                                    ->add_param($col->getFieldname())
                                    ->add_param($row)
                                    ->add_param($action)
                                    ->set_require($this->requires)
                                    ->execute();
                            if ($visibility == false) {
                                $action->addClass('d-none');
                            }
                            $action->setVisibility($visibility);
                        }


                        //call_user_func($this->cell_callback_func,$this,$col->get_fieldname(),$row,$v);
                    }


                    $this->js_cell .= $this->rowActionList->js();

                    $html->appendln($this->rowActionList->html($html->getIndent()));
                    $html->decIndent()->appendln('</td>')->br();
                }



                $html->decIndent()->appendln('</tr>')->br();
            }
        }


        $html->decIndent()->appendln('</tbody>')->br();
        //footer
        if ($this->footer) {
            $html->incIndent()->appendln('<tfoot>')->br();
            $total_column = count($this->columns);
            $addition_column = 0;
            if ($this->haveRowAction())
                $addition_column++;
            if ($this->numbering)
                $addition_column++;
            if ($this->checkbox)
                $addition_column++;

            foreach ($this->footer_field as $f) {
                $html->incIndent()->appendln('<tr>')->br();
                $colspan = $f["labelcolspan"];
                if ($colspan == 0)
                    $colspan = $total_column + $addition_column - 1;
                $html->incIndent()->appendln('<td colspan="' . ($colspan) . '">')->br();
                $html->appendln($f["label"])->br();
                $html->decIndent()->appendln('</td>')->br();
                $class = "";
                switch ($f["align"]) {
                    case "left": $class .= " align-left";
                        break;
                    case "right": $class .= " align-right";
                        break;
                    case "center": $class .= " align-center";
                        break;
                }

                $fval = $f["value"];
                if ($fval instanceof CRenderable) {
                    $html->incIndent()->appendln('<td class="' . $class . '">')->br();
                    $html->appendln($fval->html($indent))->br();
                    $html->decIndent()->appendln('</td>')->br();
                } else if (is_array($fval)) {
                    $skip_column = 0;

                    foreach ($this->columns as $col) {
                        $is_skipped = false;
                        if ($skip_column < $colspan) {
                            $skip_column++;
                            $is_skipped = true;
                        }
                        if (!$is_skipped) {
                            $fcolval = "";
                            if (isset($fval[$col->get_fieldname()])) {
                                $fcolval = $fval[$col->get_fieldname()];
                            }

                            switch ($col->get_align()) {
                                case "left": $class .= " align-left";
                                    break;
                                case "right": $class .= " align-right";
                                    break;
                                case "center": $class .= " align-center";
                                    break;
                            }
                            $html->incIndent()->appendln('<td class="' . $class . '">')->br();
                            $html->appendln($fcolval)->br();
                            $html->decIndent()->appendln('</td>')->br();
                        }
                    }
                } else {
                    $html->incIndent()->appendln('<td class="' . $class . '">')->br();
                    $html->appendln($fval)->br();
                    $html->decIndent()->appendln('</td>')->br();
                }
                $html->decIndent()->appendln('</tr>')->br();
            }
            $html->decIndent()->appendln('</tfoot>')->br();
        }
        $html->decIndent()->appendln('</table>' . $htmlResponsiveClose);

        return $html->text();
    }

    public function html($indent = 0) {


        $html = new CStringBuilder();
        $html->setIndent($indent);
        $wrapped = ($this->apply_data_table > 0) || $this->have_header_action();
        if ($wrapped) {

            $main_class = ' widget-box ';
            $main_class_title = ' widget-title ';
            $main_class_content = ' widget-content ';
            if ($this->bootstrap == '3.3') {
                $main_class = ' box box-info';
                $main_class_title = ' box-header with-border ';
                $main_class_content = ' box-body ';
            }
            if ($this->widget_title == false) {
                $main_class_title = ' ';
            }

            $html->appendln('<div class="' . $main_class . ' widget-table">')->incIndent();
            $show_title = true;
            if ($this->bootstrap == '3.3' && strlen($this->title) == 0) {
                $show_title = false;
            }
            if ($show_title) {
                $html->appendln('<div class="' . $main_class_title . '">')->incIndent();
                if (strlen($this->icon > 0)) {
                    $html->appendln('<span class="icon">')->incIndent();
                    $html->appendln('<i class="icon-' . $this->icon . '"></i>');
                    $html->decIndent()->appendln('</span');
                }
                $html->appendln('<h5>' . $this->title . '</h5>');
                if ($this->haveHeaderAction()) {
                    $html->appendln($this->headerActionList->html($html->getIndent()));

                    $this->js_cell .= $this->headerActionList->js();
                }
                $html->decIndent()->appendln('</div>');
            }
            $html->appendln('<div class="' . $main_class_content . ' nopadding">')->incIndent();
        }

        $html->append($this->rawHtml($html->getIndent()));
        if ($wrapped > 0) {
            $html->decIndent()->appendln('</div>');
            $html->decIndent()->appendln('</div>');
        }





        return $html->text();
    }

    public function js($indent = 0) {
        $ajax_url = "";
        if ($this->ajax) {
            $columns = array();
            foreach ($this->columns as $col) {
                $columns[] = $col;
            }

            $dbTemp = $this->db;
            $this->db = null;

            $ajaxMethod = CAjax::createMethod();
            $ajaxMethod->setType('DataTable');
            $ajaxMethod->setData('columns', $columns);
            $ajaxMethod->setData('query', $this->query);
            $ajaxMethod->setData('row_action_list', $this->rowActionList);
            $ajaxMethod->setData('key_field', $this->key_field);
            $ajaxMethod->setData('table', serialize($this));
            $ajaxMethod->setData('dbConfig', $this->dbConfig);
            $ajaxMethod->setData('domain', $this->domain);
            $ajaxMethod->setData('checkbox', $this->checkbox);
            $ajaxMethod->setData('is_elastic', $this->isElastic);
            $ajaxMethod->setData('is_callback', $this->isCallback);
            $ajaxMethod->setData('callback_require', $this->callbackRequire);
            $ajaxMethod->setData('callback_options', $this->callbackOptions);
            $ajax_url = $ajaxMethod->makeUrl();
            $this->db = $dbTemp;
        }

        foreach ($this->footer_action_list->childs() as $row_act) {
            $id = $row_act->id();
            if ((strpos($id, 'export_excel') !== false)) {
                $row_act->set_label('Download Excel')->set_icon('file');


                $action_url = CAjaxMethod::factory()->set_type('callback')
                        ->set_data('callable', array('CTable', 'action_download_excel'))
                        ->set_data('query', $this->query)
                        ->set_data('row_action_list', $this->rowActionList)
                        ->set_data('key_field', $this->key_field)
                        ->set_data('table', serialize($this))
                        ->makeurl();
                $row_act->add_listener('click')->add_handler('custom')->set_js("window.location.href='" . $action_url . "';");
            }
        }
        $js = new CStringBuilder();


        $js->setIndent($indent);


        $total_column = count($this->columns);
        if ($this->haveRowAction()) {
            $total_column++;
        }
        if ($this->checkbox) {
            $total_column++;
        }


        if ($this->apply_data_table > 0) {

            $length_menu = "";
            $km = "";
            $vm = "";
            foreach ($this->paging_list as $k => $v) {
                if (strlen($km) > 0)
                    $km .= ", ";
                if (strlen($vm) > 0)
                    $vm .= ", ";
                $km .= $k;
                $vm .= "'" . $v . "'";
            }
            $hs_val = $this->header_sortable ? "true" : "false";
            $js->appendln("var table = jQuery('#" . $this->id . "');")->br();
            $js->appendln("var header_sortable = " . $hs_val . ";")->br();
            $js->appendln("var vaoColumns = [];")->br();
            if ($this->numbering) {
                $aojson = array();
                $aojson["bSortable"] = false;
                $aojson["bSearchable"] = false;
                $aojson["bVisible"] = true;
                $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
                ;
            }
            if ($this->checkbox) {
                $aojson = array();
                $aojson["bSortable"] = false;
                $aojson["bSearchable"] = false;
                $aojson["bVisible"] = true;
                $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
                ;
            }

            foreach ($this->columns as $col) {
                $aojson = array();
                $aojson["bSortable"] = $col->sortable && $this->header_sortable;
                $aojson["bSearchable"] = $col->searchable;
                $aojson["bVisible"] = $col->visible;

                $js->appendln("
                            
					vaoColumns.push( " . json_encode($aojson) . " );");
            }
            if ($this->haveRowAction()) {
                $aojson = array();
                $aojson["bSortable"] = false;
                $aojson["bSearchable"] = false;
                $aojson["bVisible"] = true;
                $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
            }



            $js->appendln("var tableStyled_" . $this->id . " = false;")->br()->
                    appendln("var oTable = table.dataTable({")->br()->incIndent();


//            $js->appendln("responsive: {
//        details: {
//            renderer: $.fn.dataTable.Responsive.renderer.tableAll()
//        }
//    },");
            if ($this->ajax) {
                $js->append("")
                        ->appendln("'bRetrieve': true,")->br()
                        ->appendln("'bProcessing': true,")->br()
                        ->appendln("'bServerSide': true,")->br()
                        ->appendln("'sAjaxSource': '" . $ajax_url . "',")->br()
                        ->appendln("'sServerMethod': '" . strtoupper($this->ajax_method) . "',")->br()
                        ->appendln("'fnServerData': function ( sSource, aoData, fnCallback, oSettings ) {
                                        var data_quick_search = [];
                                        jQuery('.data_table-quick_search').each(function(){
                                            if (jQuery(this).val() != '') {
                                                var input_name = jQuery(this).attr('name');
                                                var cur_transforms = jQuery(this).attr('transforms');
                                                data_quick_search.push({'name': input_name, 'value': jQuery(this).val(), 'transforms': cur_transforms});
                                            }
                                        });
                                        aoData.push({'name': 'dttable_quick_search', 'value': JSON.stringify(data_quick_search)});
                                        oSettings.jqXHR = $.ajax( {
                                            'dataType': 'json',
                                            'type': '" . strtoupper($this->ajax_method) . "',
                                            'url': sSource,
                                            'data': aoData,
                                            'success': function(data) {
                                                fnCallback(data.datatable);
                                                if(data.js && data.js.length>0) {
                                                    var script = $.cresenity.base64.decode(data.js);
                                                    eval(script);
                                                }

                                            },
                                            'error': function(a,b,c) {
                                                $.cresenity.message(a);
                                            }
                                        })
                                    },
                                    ")
                        ->appendln("'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
						// Bold the grade for all 'A' grade browsers
						
							//$('td:eq(4)', nRow).html( '<b>A</b>' );
						//$.cresenity.set_confirm($('a.confirm',nRow));
						
						var footer_action = $('#" . $this->id . "_wrapper .footer_action');
						
						" . ($this->have_footer_action() ? "footer_action.html(" . json_encode($this->footer_action_list->html()) . ");" : "") . " 
           
						" . ($this->have_footer_action() ? "" . $this->footer_action_list->js() . "" : "") . " 
						
						footer_action.css('position','absolute').css('left','275px').css('margin','4px 8px 2px 10px');
						
						for(i=0;i<$(nRow).find('td').length;i++) {
							
							//get head data align
							var data_align = $('#" . $this->id . "').find('thead th:eq('+i+')').data('align');
							var data_action = $('#" . $this->id . "').find('thead th:eq('+i+')').data('action');
							var data_no_line_break = $('#" . $this->id . "').find('thead th:eq('+i+')').data('no-line-break');
							if(data_action) {
								$('td:eq('+i+')', nRow).addClass(data_action);
							}
							if(data_align) {
								$('td:eq('+i+')', nRow).addClass(data_align);
							}
							if(data_no_line_break) {
								$('td:eq('+i+')', nRow).addClass(data_no_line_break);
							}
						}
						
						
					},
				")
                        ->appendln("'fnInitComplete': function() {
					this.fnAdjustColumnSizing(true);
					},
				")
                ;
            }
            /*
              $js->append("")
              ->appendln("'sScrollX': '100%',")->br()
              ->appendln("'bScrollCollapse': true,")->br()
              ;
             */


            $jqueryui = "'bJQueryUI': false,";
            if (CClientModules::instance()->is_registered_module('jquery.ui') || CClientModules::instance()->is_registered_module('jquery-ui-1.12.1.custom')) {
                $jqueryui = "'bJQueryUI': true,";
            }

            $js->append("")
                    ->appendln("'oLanguage': {
						'sLoadingRecords': '" . clang::__('Loading') . "',
						'sZeroRecords': '" . clang::__('No records to display') . "',
						'responsive': true,
						'oPaginate': {
							'sFirst': '" . clang::__('First') . "',
							'sPrevious': '" . clang::__('Previous') . "',
							'sNext': '" . clang::__('Next') . "',
							'sLast': '" . clang::__('Last') . "'
							
						}
					},")->br()
                    ->appendln($jqueryui)->br()
                    ->appendln("'bStateSave': false,")->br()
                    ->appendln("'iDisplayLength': " . $this->display_length . ",")->br()
                    ->appendln("'bSortCellsTop': " . $hs_val . ",")->br()
                    ->appendln("'aaSorting': [],")->br()
                    ->appendln("'oLanguage': { 
						sSearch : '" . clang::__('Search') . "',
						sProcessing : '" . clang::__('Processing') . "',
						sLengthMenu  : '" . clang::__('Show') . " _MENU_ " . clang::__('Entries') . "',
						oPaginate  : {'sFirst' : '" . clang::__('First') . "',
                                                'sLast' : '" . clang::__('Last') . "',
                                                'sNext' : '" . clang::__('Next') . "',
                                                'sPrevious' : '" . clang::__('Previous	') . "'},
                                                sinfo: '" . clang::__('Showing') . " _START_ " . clang::__('to') . " _END_ " . clang::__('of') . " _TOTAL_ " . clang::__('entries') . "',
						sInfoEmpty  : '" . clang::__('No data available in table') . "',
						sEmptyTable  : '" . clang::__('No data available in table') . "',
						sInfoThousands   : '" . clang::__('') . "',
					},")->br()
                    ->appendln("'bDeferRender': " . ($this->get_option("defer_render") ? "true" : "false") . ",")->br()
                    ->appendln("'bFilter': " . ($this->get_option("filter") ? "true" : "false") . ",")->br()
                    ->appendln("'bInfo': " . ($this->get_option("info") ? "true" : "false") . ",")->br()
                    ->appendln("'bPaginate': " . ($this->get_option("pagination") ? "true" : "false") . ",")->br()
                    ->appendln("'bLengthChange': " . ($this->get_option("length_change") ? "true" : "false") . ",")->br()
                    ->appendln("'aoColumns': vaoColumns,")->br()
                    ->appendln("'autoWidth': false,")->br()
                    ->appendln("'aLengthMenu': [
					[" . $km . "],
					[" . $vm . "]
				],")->br()
            ;

            /*
              $js->append("")
              ->appendln("'sScrollX': '100%',")->br()
              ->appendln("'sScrollXInner': '100%',")->br()
              ->appendln("'bScrollCollapse': true,")->br()
              ;
             */

            // if ($this->bootstrap == '3') {
            if ($this->bootstrap >= '3') {
                if ($this->dom == null) {
                    $this->dom = "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>";
                }
            }

            if ($this->dom == null) {
                $this->dom = "<\"\"l>t<\"F\"<\".footer_action\">frp>";
            } else {
                $this->dom = str_replace("'", "\'", $this->dom);
            }
            $js->append("")
                    ->appendln("'sPaginationType': 'full_numbers',")->br()
                    ->appendln("'sDom': '" . $this->dom . "',")->br();

            /*
              $js->append("
              'fnDrawCallback': function ( oSettings ) {");

              if(strlen($this->group_by)>0) {
              $col_ind = false;
              $inc = 0;
              foreach($this->columns as $col) {

              if($col->get_fieldname()==$this->group_by) {
              $col_ind=$inc;
              break;
              }
              $inc++;
              }

              if($col_ind>=0) {
              $js->appendln("
              if ( oSettings.aiDisplay.length >= 0 ) {
              var nTrs = $('#".$this->id." tbody tr');
              var iColspan = nTrs[0].getElementsByTagName('td').length;
              var sLastGroup = '';
              for ( var i=0 ; i<nTrs.length ; i++ )
              {
              var iDisplayIndex = oSettings._iDisplayStart + i;
              var sGroup = oSettings.aoData[ oSettings.aiDisplay[iDisplayIndex] ]._aData[".$col_ind."];
              if ( sGroup != sLastGroup )
              {
              var nGroup = document.createElement( 'tr' );
              var nCell = document.createElement( 'td' );
              nCell.colSpan = iColspan;
              nCell.className = 'group';
              nCell.innerHTML = sGroup;
              nGroup.appendChild( nCell );
              nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );
              sLastGroup = sGroup;
              }
              }
              }
              ");
              }
              }

              $js->append("
              },");
             */

            $js->append("")
                    ->decIndent()->appendln("});")->br();


//                $js->append("oTable.columns().every( function () {
//                                var that = this;
//
//                                $( 'input', this.footer() ).on( 'keyup change', function () {
//                                    that
//                                        .search( this.value )
//                                        .draw();
//                                } );
//                            } );");
            //$js->appendln("oTable.fnSortOnOff( '_all', false );")->br();

            $js->appendln('function buildFilters_' . $this->id . '() {')->br()
                    ->appendln("var quick_search = jQuery('<tr>');")->br()
                    ->appendln("jQuery('#" . $this->id . " thead th').each( function (i) {
                            var title = jQuery('#" . $this->id . " thead th').eq( jQuery(this).index() ).text();
                            var have_action = " . ($this->haveRowAction() ? "1" : "0") . ";
                            
                           
                            var total_th = jQuery('#" . $this->id . " thead th').length;
                            var input = '';
                            var have_checkbox = " . ($this->checkbox ? "1" : "0") . ";
                                
                            if((!(have_action==1&&(total_th-1==jQuery(this).index())))&& (!(have_checkbox==1&&(0==jQuery(this).index()))) ) {
                                var i2 = 0;
                                if(have_checkbox) {
                                        i2 = -1;
                                }
                            
                                var all_column = " . json_encode($this->columns) . ";
                                var column = all_column[jQuery(this).index()+i2];
                                var transforms = {};
                                if(column) {
                                    if(hasOwnProperty.call(column, 'transforms')) {
                                        
                                        transforms = JSON.stringify(column.transforms);
                                    }
                               
                                    if(column.searchable) {
                                        input = jQuery('<input>');
                                        input.attr('type', 'text');
                                        input.attr('name', 'dt_table_qs-' + jQuery(this).attr('field_name'));
                                        input.attr('class', 'data_table-quick_search');

                                        input.attr('transforms', transforms);
                                        input.attr('placeholder', 'Search ' + title );
                                    }
                                }
                                
                            }
                            var td = jQuery('<td>').append(input);
                            quick_search.append(td);
                        });")->br()
                    ->appendln("table.children('thead').append(quick_search);")->br()
                    ->appendln('}')->br()
                    ->appendln('var dttable_quick_search = ' . ($this->quick_search ? "1" : "0") . ';')->br()
                    ->appendln('if (dttable_quick_search == "1") { buildFilters_' . $this->id . '(); }')
            ;

            $js->appendln("jQuery('.data_table-quick_search').on('keyup', function(){
                            table.fnClearTable( 0 );
                            table.fnDraw();
                        });")
            ;
        }
        if ($this->checkbox) {
            $js->appendln("
				jQuery('#" . $this->id . "-check-all').click(function() {
					
					if(jQuery(this).is(':checked')) {
						jQuery('.checkbox-" . $this->id . "').attr('checked','checked');
						jQuery('.checkbox-" . $this->id . "').prop('checked',true);
					} else {
						jQuery('.checkbox-" . $this->id . "').removeAttr('checked');
						jQuery('.checkbox-" . $this->id . "').prop('checked',false);
					}
				});
				
			");
        }
        $js->appendln($this->js_cell);
        if (!$this->ajax) {
            $js->append(parent::js($indent))->br();
            foreach ($this->data as $row) {
                if ($row instanceof CRenderable) {
                    $js->appendln($row->js())->br();
                    continue;
                }
                foreach ($row as $row_k => $row_v) {
                    if ($row_v instanceof CRenderable) {
                        $js->appendln($row_v->js())->br();
                    }
                }
            }
        }

        if ($this->footer) {

            foreach ($this->footer_field as $f) {
                $fval = $f["value"];
                if ($fval instanceof CRenderable) {
                    $js->appendln($fval->js())->br();
                }
            }
        }

//            echo '<textarea>' . $js->text() . '</textarea>';
//            clog::write($js->text());
        return $js->text();
    }

}

?>