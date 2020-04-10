<?php

class CElement_Component_DataTable extends CElement_Component {

    use CTrait_Compat_Element_DataTable,
        CTrait_Element_ActionList_Row,
        CTrait_Element_ActionList_Header,
        CElement_Component_DataTable_Trait_GridViewTrait,
        CElement_Component_DataTable_Trait_ExportTrait,
        CElement_Component_DataTable_Trait_JavascriptTrait;

    public $defaultPagingList = array(
        "10" => "10",
        "25" => "25",
        "50" => "50",
        "100" => "100",
        "-1" => "ALL",
    );
    public $current_row = 1;
    public $dbName;
    public $dbConfig;
    public $columns;
    public $footer;
    public $footer_field;
    public $requires = array();
    public $data;
    public $keyField;
    public $checkbox;
    public $checkboxColumnWidth;
    public $checkbox_value;
    public $numbering;
    public $query;
    public $customColumnHeader;
    public $header_sortable;
    public $cellCallbackFunc;
    public $filterActionCallbackFunc;
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
    public $searchPlaceholder = '';
    public $infoText = '';
    protected $actionLocation = 'last';
    protected $tableStriped;
    protected $tableBordered;
    protected $quick_search = FALSE;
    protected $tbodyId;
    protected $js_cell;
    protected $dom = null;
    protected $widget_title;
    protected $fixedColumn;
    protected $scrollX;
    protected $scrollY;

    public function __construct($id = "") {
        parent::__construct($id);
        $this->defaultPagingList["-1"] = clang::__("ALL");
        $this->tag = "table";
        $this->responsive = false;

        $db = CDatabase::instance();

        $this->dbConfig = $db->config();
        $this->dbName = $db->getName();
        $this->display_length = "10";
        $this->paging_list = $this->defaultPagingList;
        $this->options = CElement_Component_DataTable_Options::factory();
        $this->data = array();
        $this->keyField = "";
        $this->columns = array();
        $this->rowActionList = CElement_Factory::createList('ActionList');
        $this->rowActionList->setStyle('btn-icon-group')->addClass('btn-table-action');
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
        $this->cellCallbackFunc = "";
        $this->filterActionCallbackFunc = "";
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

        $this->customColumnHeader = "";
        $this->show_header = true;
        $this->apply_data_table = true;
        $this->group_by = "";
        $this->icon = "";
        $this->pdf_font_size = 8;
        $this->pdf_orientation = 'P';
        $this->requires = array();
        $this->js_cell = '';
        $this->quick_search = FALSE;
        $this->tbodyId = '';

        $this->report_header = array();


        $this->widget_title = true;

        //$this->add_footer_action($this->id.'_export_excel');

        $this->export_filename = $this->id;
        $this->export_sheetname = $this->id;
        $this->tableStriped = true;
        $this->tableBordered = true;
        $this->haveDataTableViewAction = false;
        $this->dataTableView = CConstant::TABLE_VIEW_ROW;
        $this->dataTableViewColCount = 5;
        $this->fixedColumn = false;
        $this->scrollX = false;
        $this->scrollY = false;


        $this->infoText = clang::__('Showing') . " _START_ " . clang::__('to') . " _END_ " . clang::__('of') . " _TOTAL_ " . clang::__('entries') . "";
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
        $this->actionLocation = CManager::theme()->getData('table.actionLocation', 'last');
    }

    public static function factory($id = "") {
        return new CElement_Component_DataTable($id);
    }

    public function setScrollY($bool = true) {
        $this->scrollY = $bool;
        return $this;
    }

    /**
     * 
     * @param string $actionLocation
     * @return $this
     * @throws Exception
     */
    public function setActionLocation($actionLocation) {
        if (!in_array($actionLocation, array('first', 'last'))) {
            throw new Exception('action location parameter must be first or last');
        }
        $this->actionLocation = $actionLocation;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getActionLocation() {
        return $this->actionLocation;
    }

    public function setScrollX($bool = true) {
        $this->scrollX = $bool;
        return $this;
    }

    public function setDomain($domain) {
        parent::setDomain($domain);
        $this->setDatabase(CDatabase::instance(null, null, $domain));
        return $this;
    }

    public function setDatabase($db, $dbConfig = null) {
        if ($db instanceof CDatabase) {
            $this->dbName = $db->getName();
            $this->dbConfig = $db->config();
        } else {
            $this->dbName = $db;
            $this->dbConfig = $dbConfig;
        }


        return $this;
    }

    public function setSearchPlaceholder($placeholder) {
        $this->searchPlaceholder = $placeholder;

        return $this;
    }

    public function setInfoText($infoText) {
        $this->infoText = $infoText;

        return $this;
    }

    public function setFixedColumn($bool = true) {
        $this->fixedColumn = $bool;

        return $this;
    }

    public function setCheckboxColumnWidth($width) {
        $this->checkboxColumnWidth = $width;
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
        $this->customColumnHeader = $html;
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
        $this->tbodyId = $id;
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
        $this->requery();
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
        $this->cellCallbackFunc = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function filterActionCallbackFunc($func, $require = "") {
        $this->filterActionCallbackFunc = $func;
        if (strlen($require) > 0) {
            $this->requires[] = $require;
        }
        return $this;
    }

    public function setKey($fieldname) {
        $this->keyField = $fieldname;
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
     * @return $this
     */
    public function requery() {

        if (!$this->isElastic && !$this->isCallback) {
            if ($this->ajax == false) {
                if (strlen($this->query) > 0) {
                    $r = $this->db()->query($this->query);
                    $this->data = $r->result(false);
                }
            } else {
                $this->data = array();
            }
        }

        return $this;
    }

    /**
     * 
     * @param string $q
     * @return $this
     */
    public function setDataFromQuery($q) {
        if ($this->ajax == false) {
            $r = $this->db()->query($q);
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
        $sql = $this->db()->compileBinds($modelQuery->toSql(), $modelQuery->getBindings());
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
        $this->query = CHelper::closure()->serializeClosure($callback);
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

    protected function rawTBody($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);

        $tbodyId = (strlen($this->tbodyId) > 0 ? "id='" . $this->tbodyId . "' " : "");
        $js = "";
        $html->appendln('<tbody ' . $tbodyId . '>')->incIndent()->br();
        //render body;
        $html->appendln($this->htmlChild($indent));
        $no = 0;
        if (!$this->ajax && (is_array($this->data) || $this->data instanceof Traversable)) {
            foreach ($this->data as $row) {
                if ($row instanceof CRenderable) {
                    $html->appendln($row->html());
                    continue;
                }

                $no++;
                $key = "";

                if (array_key_exists($this->keyField, $row)) {

                    $key = $row[$this->keyField];
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
                if ($this->actionLocation == 'first') {
                    $js .= $this->drawActionAndGetJs($html, $row, $key);
                }
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
                    //if have callback
                    if ($col->callback != null) {
                        $col_v = CFunction::factory($col->callback)
                                // ->addArg($table)
                                ->addArg($row)
                                ->addArg($col_v)
                                ->setRequire($col->callbackRequire)
                                ->execute();
                        if (is_array($col_v) && isset($col_v['html']) && isset($col_v['js'])) {
                            $js .= $col_v['js'];
                            $col_v = $col_v['html'];
                        }
                    }
                    $new_v = $col_v;

                    if (($this->cellCallbackFunc) != null) {
                        $new_v = CFunction::factory($this->cellCallbackFunc)
                                ->addArg($this)
                                ->addArg($col->getFieldname())
                                ->addArg($row)
                                ->addArg($new_v)
                                ->setRequire($this->requires)
                                ->execute();
                        if (is_array($new_v) && isset($new_v['html']) && isset($new_v['js'])) {
                            $js .= $new_v['js'];
                            $new_v = $new_v['html'];
                        }
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
                    if ($col->getHiddenPhone())
                        $class .= " hidden-phone";

                    if ($col->getHiddenTablet())
                        $class .= " hidden-tablet";

                    if ($col->getHiddenDesktop())
                        $class .= " hidden-desktop";

                    $pdfTBodyTdCurrentAttr = $this->getPdfTBodyTdAttr();
                    if ($this->export_pdf) {
                        switch ($col->getAlign()) {
                            case "left": $pdfTBodyTdCurrentAttr .= ' align="left"';
                                break;
                            case "right": $pdfTBodyTdCurrentAttr .= ' align="right"';
                                break;
                            case "center": $pdfTBodyTdCurrentAttr .= ' align="center"';
                                break;
                        }
                    }
                    if (is_array($new_v)) {
                        $this->js_cell .= carr::get($new_v, 'js', '');
                        $new_v = carr::get($new_v, 'html', '');
                    }

                    $html->appendln('<td' . $pdfTBodyTdCurrentAttr . ' class="' . $class . '" data-column="' . $col->getFieldname() . '">' . $new_v . '</td>')->br();
                    $col_found = true;
                }
                if ($this->actionLocation == 'last') {
                    $js .= $this->drawActionAndGetJs($html, $row, $key);
                }



                $html->decIndent()->appendln('</tr>')->br();
            }
        }
        $this->js_cell .= $js;

        $html->decIndent()->appendln('</tbody>')->br();
        return $html->text();
    }

    protected function drawActionAndGetJs(CStringBuilder $html, $row, $key) {
        $js = '';
        if ($this->haveRowAction()) {
            $html->appendln('<td class="low-padding align-center cell-action td-action">')->incIndent()->br();
            foreach ($row as $k => $v) {
                $jsparam[$k] = $v;
            }

            $jsparam["param1"] = $key;
            if ($this->getRowActionStyle() == "btn-dropdown") {
                $this->rowActionList->addClass("pull-right");
            }
            $this->rowActionList->regenerateId(true);
            $this->rowActionList->apply("setJsParam", $jsparam);
            $this->rowActionList->apply("setHandlerUrlParam", $jsparam);

            if (($this->filterActionCallbackFunc) != null) {
                $actions = $this->rowActionList->childs();

                foreach ($actions as &$action) {
                    $visibility = CFunction::factory($this->filterActionCallbackFunc)
                            ->addArg($this)
                            ->addArg('action')
                            ->addArg($row)
                            ->addArg($action)
                            ->setRequire($this->requires)
                            ->execute();
                    if ($visibility == false) {
                        $action->addClass('d-none');
                    }
                    $action->setVisibility($visibility);
                }
            }


            $js = $this->rowActionList->js();

            $html->appendln($this->rowActionList->html($html->getIndent()));
            $html->decIndent()->appendln('</td>')->br();
        }
        return $js;
    }

    protected function rawHtml($indent = 0) {
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
            if (strlen($this->customColumnHeader) > 0) {
                $html->appendln($this->customColumnHeader);
            } else {
                $html->appendln('<tr>')
                        ->incIndent()->br();

                if ($this->numbering) {
                    $html->appendln('<th data-align="align-right" class="' . $thClass . '" width="20" scope="col">No</th>')->br();
                }
                if ($this->checkbox) {
                    $attrWidth = "";
                    if (strlen($this->checkboxColumnWidth) > 0) {
                        $attrWidth = 'width="' . $this->checkboxColumnWidth . '"';
                    }
                    $html->appendln('<th class="align-center" data-align="align-center" class="' . $thClass . '" scope="col" ' . $attrWidth . '><input type="checkbox" name="' . $this->id . '-check-all" id="' . $this->id . '-check-all" value="1"></th>')->br();
                }
                if ($this->getActionLocation() == 'first') {
                    if ($this->haveRowAction()) {
                        $action_width = 31 * $this->rowActionCount() + 5;
                        if ($this->getRowActionStyle() == "btn-dropdown") {
                            $action_width = 70;
                        }
                        $html->appendln('<th data-action="cell-action td-action" data-align="align-center" scope="col" width="' . $action_width . '" class="align-center cell-action th-action' . $thClass . '">' . clang::__('Actions') . '</th>')->br();
                    }
                }
                foreach ($this->columns as $col) {
                    $html->appendln($col->renderHeaderHtml($this->export_pdf, $thClass, $html->getIndent()))->br();
                }
                if ($this->getActionLocation() == 'last') {
                    if ($this->haveRowAction()) {
                        $action_width = 31 * $this->rowActionCount() + 5;
                        if ($this->getRowActionStyle() == "btn-dropdown") {
                            $action_width = 70;
                        }
                        $html->appendln('<th data-action="cell-action td-action" data-align="align-center" scope="col" width="' . $action_width . '" class="align-center cell-action th-action' . $thClass . '">' . clang::__('Actions') . '</th>')->br();
                    }
                }
                $html->decIndent()->appendln("</tr>")->br();
            }
            $html->decIndent()->appendln("</thead>")->br();
        }

        $html->append($this->rawTBody($html->getIndent()));


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

        $this->buildOnce();
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $wrapped = ($this->apply_data_table > 0) || $this->haveHeaderAction() || strlen($this->title) > 0;
        if ($wrapped) {

            $mainClass = ' widget-box ';
            $mainClassTitle = ' widget-title ';
            $tableViewClass = $this->dataTableView == CConstant::TABLE_VIEW_COL ? ' data-table-col-view' : ' data-table-row-view';
            $mainClassContent = ' widget-content ' . $tableViewClass . ' col-view-count-' . $this->dataTableViewColCount;
            if ($this->bootstrap == '3.3') {
                $mainClass = ' box box-info';
                $mainClassTitle = ' box-header with-border ';
                $mainClassContent = ' box-body data-table-row-view';
            }
            if ($this->widget_title == false) {
                $mainClassTitle = ' ';
            }
            if ($this->haveDataTableViewAction) {
                $mainClassTitle .= ' with-elements';
            }
            $html->appendln('<div id="' . $this->id() . '-widget-box" class="' . $mainClass . ' widget-table">')->incIndent();
            $showTitle = true;
            if ($this->bootstrap == '3.3' && strlen($this->title) == 0) {
                $showTitle = false;
            }
            if ($showTitle) {
                $html->appendln('<div class="' . $mainClassTitle . '">')->incIndent();
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

                if ($this->haveDataTableViewAction) {
                    $colViewActionActive = $this->dataTableView == CConstant::TABLE_VIEW_COL ? ' active' : '';
                    $rowViewActionActive = $this->dataTableView == CConstant::TABLE_VIEW_ROW ? ' active' : '';
                    $colViewActionChecked = $this->dataTableView == CConstant::TABLE_VIEW_COL ? ' checked="checked"' : '';
                    $rowViewActionChecked = $this->dataTableView == CConstant::TABLE_VIEW_ROW ? ' checked="checked"' : '';
                    $html->appendln('
                        <div class="btn-group btn-group-toggle ml-auto" data-toggle="buttons">
                            <label class="btn btn-default icon-btn md-btn-flat ' . $colViewActionActive . '">
                                <input type="radio" name="' . $this->id() . '-data-table-view" value="data-table-col-view" ' . $colViewActionChecked . ' />
                                <span class="ion ion-md-apps"></span>
                            </label>
                            <label class="btn btn-default icon-btn md-btn-flat ' . $rowViewActionActive . '">
                                <input type="radio" name="' . $this->id() . '-data-table-view" value="data-table-row-view" ' . $rowViewActionChecked . '" />
                                <span class="ion ion-md-menu"></span>
                            </label>
                        </div>
                    ');
                }
                $html->decIndent()->appendln('</div>');
            }
            $html->appendln('<div class="' . $mainClassContent . ' nopadding">')->incIndent();
        }

        $html->append($this->rawHtml($html->getIndent()));
        if ($wrapped > 0) {
            $html->decIndent()->appendln('</div>');
            $html->decIndent()->appendln('</div>');
        }





        return $html->text();
    }

    /**
     * 
     * @return CDatabase
     */
    public function db() {
        return CDatabase::instance($this->dbName, $this->dbConfig, $this->domain);
    }

    
    /**
     * 
     * @return string
     */
    public function getKeyField() {
        return $this->keyField;
    }
}

?>