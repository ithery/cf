<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CElement_Component_DataTable_Trait_HtmlTrait {

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

}
