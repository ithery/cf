<?php

/**
 * Description of TableRenderer
 *
 * @author Hery
 */
class CProfiler_TableRenderer {

    protected $columns = array();
    protected $rows = array();

    /**
     * Add column to table.
     *
     * @param  string  CSS class
     * @param  string  CSS style
     */
    public function addColumn($class = '', $style = '') {
        $this->columns[] = array('class' => $class, 'style' => $style);
    }

    /**
     * Add row to table.
     *
     * @param  array   data to go in table cells
     * @param  string  CSS class
     * @param  string  CSS style
     */
    public function addRow($data, $class = '', $style = '') {
        $this->rows[] = array('data' => $data, 'class' => $class, 'style' => $style);
    }

    /**
     * Render table.
     *
     * @return  string
     */
    public function render() {
        $rows = $this->rows;
        $columns = $this->columns;

        $html = '';
        $html .= '<table class="kp-table">';
        foreach ($rows as $row) {

            $class = empty($row['class']) ? '' : ' class="' . $row['class'] . '"';
            $style = empty($row['style']) ? '' : ' style="' . $row['style'] . '"';
            $html .= '<tr' . $class . $style . '>';
            foreach ($columns as $index => $column) {
                $class = empty($column['class']) ? '' : ' class="' . $column['class'] . '"';
                $style = empty($column['style']) ? '' : ' style="' . $column['style'] . '"';
                $value = $row['data'][$index];
                $value = (is_array($value) OR is_object($value)) ? '<pre>' . c::e(print_r($value, TRUE)) . '</pre>' : c::e($value);
                $html .= '<td' . $style . $class . '>' . $value . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

}
