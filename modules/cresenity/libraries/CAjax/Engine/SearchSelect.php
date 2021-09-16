<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 12:46:23 AM
 */
class CAjax_Engine_SearchSelect extends CAjax_Engine {
    public function execute() {
        $input = $this->input;
        $data = $this->ajaxMethod->getData();

        $q = carr::get($data, 'query');
        $key_field = carr::get($data, 'keyField', carr::get($data, 'key_field'));

        $search_field = carr::get($data, 'searchField', carr::get($data, 'search_field'));

        $db = CDatabase::instance();

        $term = '';
        $limit = '';
        $page = '';

        $callback = carr::get($input, 'callback', '');
        $term = carr::get($input, 'q', carr::get($input, 'term', ''));
        $limit = carr::get($input, 'limit', '');
        $page = carr::get($input, 'page', '');

        $valueCallbackFunction = carr::get($data, 'valueCallback', null);

        $base_q = $q;
        $pos_order_by = strpos(strtolower($base_q), 'order by', strpos(strtolower($base_q), 'from'));

        $pos_last_kurung = strrpos(strtolower($base_q), ')');
        if (isset($_GET['bdebug'])) {
            cdbg::var_dump($data);
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
        $sLimit = '';
        if (strlen($limit) > 0) {
            if (strlen($page) > 0) {
                $sLimit = 'LIMIT ' . ((intval($page) - 1) * 10) . ', ' . intval($limit);
            } else {
                $sLimit = 'LIMIT ' . intval($limit);
            }
        }

        /* Ordering */
        $sOrder = '';
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

        $sWhere = '';
        if (strlen($term) > 0 && (!empty($search_field))) {
            $sWhere = 'WHERE (';
            if (is_array($search_field)) {
                foreach ($search_field as $f) {
                    $sWhere .= '`' . $f . "` LIKE '%" . $db->escapeLike($term) . "%' OR ";
                }
            } else {
                $sWhere .= '`' . $search_field . "` LIKE '%" . $db->escapeLike($term) . "%' OR ";
            }

            $sWhere = substr_replace($sWhere, '', -3);
            $sWhere .= ')';

            //order
            if (is_array($search_field)) {
                foreach ($search_field as $f) {
                    if (strlen($sOrder) > 0) {
                        $sOrder .= ',';
                    }
                    $sOrder .= '`' . $f . '` = ' . $db->escape($term) . ' DESC';
                }
            }
        }

        if (strlen($sOrder) > 0) {
            $sOrder = ' ORDER BY ' . $sOrder;
            $temp_order_by = '';
        }

        if (strlen($temp_order_by) > 0) {
            $sub = explode(',', substr($temp_order_by, 9));
            $temp_order_by = '';
            foreach ($sub as $val) {
                $kata = explode('.', $val);
                if (isset($kata[1])) {
                    $temp_order_by .= ', ' . $kata[1];
                } else {
                    $temp_order_by .= ', ' . $kata[0];
                }
            }
            $temp_order_by = substr($temp_order_by, 2);
            $temp_order_by = 'ORDER BY ' . $temp_order_by;
        }

        $qfilter = 'select * from (' . $base_q . ') as a ' . $sWhere . ' ' . $sOrder;
        $total = cdbutils::get_row_count_from_base_query($qfilter);

        $qfilter .= ' ' . $temp_order_by . ' ' . $sLimit;

        $r = $db->query($qfilter);

        $result = $r->result(false);
        $data = [];
        foreach ($r as $row) {
            $p = [];
            foreach ($row as $k => $v) {
                if ($valueCallbackFunction != null && is_callable($valueCallbackFunction)) {
                    $v = call_user_func($valueCallbackFunction, $row, $k, $v);
                }
                $p[$k] = ($v == null) ? '' : $v;
            }
            if (strlen($key_field) > 0) {
                $p['id'] = carr::get($row, $key_field);
            }
            //$p["id"]=$row["item_id"];
            $data[] = $p;
        }
        $result = [];
        $result['data'] = $data;
        $result['total'] = $total;

        $response = '';
        $response .= $callback . '(';
        $response .= json_encode($result);
        $response .= ')';
        return $response;
    }
}
