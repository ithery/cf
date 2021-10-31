<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Helper_SqlHelper {

    public static function chunkSqlResult($sql, $count, callable $callback, $db = null) {
        if ($db == null) {
            $db = CDatabase::instance();
        }

        $page = 1;

        do {
            
            $sLimit = "limit ". ($page - 1) * $count.",".$count;
            
            
            $results = $db->query($sql.$sLimit);
            

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results->result(false), $page) === false) {
                return false;
            }

            unset($results);

            $page++;
        } while ($countResults == $count);

        return true;
    }

}
