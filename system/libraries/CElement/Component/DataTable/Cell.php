<?php
use Carbon\Carbon;

class CElement_Component_DataTable_Cell {
    /**
     * @var CElement_Component_DataTable
     */
    protected $table;

    /**
     * @var CElement_Component_DataTable_Column
     */
    protected $column;

    protected $row;

    protected $html;

    protected $js;

    public function __construct(CElement_Component_DataTable $table, CElement_Component_DataTable_Column $column, $row) {
        $this->table = $table;
        $this->column = $column;
        $this->row = $row;

        $this->processHtmlJs();
    }

    protected function processHtmlJs() {
        $html = null;
        $js = '';

        if ($this->row instanceof CModel) {
            $fieldName = $this->column->getFieldname();
            if (strpos($fieldName, '.') !== false) {
                $fields = explode('.', $fieldName);
                $html = $this->row;

                foreach ($fields as $fieldIndex => $field) {
                    if ($html instanceof  CModel_Collection) {
                        $remainFields = array_slice($fields, $fieldIndex);

                        $remainFieldsPath = implode('.', $remainFields);
                        $html = $html->implode($remainFieldsPath, ',');

                        break;
                    } else {
                        $html = c::optional($html)->$field;
                    }
                }
            } else {
                $html = $this->row->{$this->column->getFieldname()};
            }
        } else {
            $html = carr::get($this->row, $this->column->getFieldname());
        }
        if ($html instanceof CCollection) {
            $html = $html->toArray();
        }

        //do transform
        $html = $this->column->applyTransform($html, $this->row);

        //if formatted
        if (strlen($this->column->getFormat()) > 0) {
            $tempValue = $this->column->getFormat();
            foreach ($this->row as $k2 => $v2) {
                if (strpos($tempValue, '{' . $k2 . '}') !== false) {
                    $tempValue = str_replace('{' . $k2 . '}', $v2, $tempValue);
                }
                $html = $tempValue;
            }
        }

        //if have callback
        if ($this->column->callback != null) {
            $html = CFunction::factory($this->column->callback)
                ->addArg($this->row)
                ->addArg($html)
                ->setRequire($this->column->callbackRequire)
                ->execute();
            list($html, $jsCell) = $this->getHtmlJsCell($html);

            $js .= $jsCell;
        }

        if (($this->table->cellCallbackFunc) != null) {
            $html = CFunction::factory($this->table->cellCallbackFunc)
                ->addArg($this->table)
                ->addArg($this->column->getFieldname())
                ->addArg($this->row)
                ->addArg($html)
                ->setRequire($this->table->requires)
                ->execute();
            list($html, $jsCell) = $this->getHtmlJsCell($html);
            $js .= $jsCell;
        }

        if (!is_string($html)) {
            list($html, $jsCell) = $this->getHtmlJsCell($html);
            $js .= $js;
        }

        $this->html = $html;
        $this->js = $js;
    }

    public static function getHtmlJsCell($cell) {
        $html = '';
        $js = '';

        if (is_string($cell) || is_numeric($cell)) {
            $html = $cell;
        }

        if ($cell instanceof CRenderable) {
            $html = $cell->html();
            $js = $cell->js();
        }
        if ($cell instanceof Carbon) {
            $html = $cell->format('Y-m-d H:i:s');
        }
        if (carr::accessible($cell)) {
            $html = carr::get($cell, 'html');
            $js = carr::get($cell, 'js');
        }

        return [$html, $js];
    }

    public function html() {
        return $this->html;
    }

    public function js() {
        return $this->js;
    }
}
