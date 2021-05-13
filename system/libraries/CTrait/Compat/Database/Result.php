<?php

// @codingStandardsIgnoreStart
trait CTrait_Compat_Database_Result {
    /**
     * Returns the insert id from the result.
     *
     * @return mixed
     *
     * @deprecated use insertId
     */
    public function insert_id() {
        return $this->insertId();
    }
}
