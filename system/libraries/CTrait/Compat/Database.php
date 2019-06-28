<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 22, 2019, 2:10:37 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Database {
    
    /**
     * 
     * @deprecated since version 1.2
     * @return string
     */
    public function driver_name() {
        return $this->driverName();
    }
    
    /**
     * 
     * @deprecated
     * @param string $str
     * @return string
     */
    public function escape_like($str) {
        return $this->escapeLike($str);
    }
    
    /**
     * 
     * @deprecated since version 1.2
     * @return boolean
     */
    public function in_transaction() {
        return $this->inTransaction();
    }
    
    /**
     * Returns the last query run.
     *
     * @deprecated
     * @return  string SQL
     */
    public function last_query() {
        return $this->lastQuery();
    }
    
    
    
}
