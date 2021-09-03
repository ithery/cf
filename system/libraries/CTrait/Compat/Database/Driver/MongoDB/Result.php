<?php

// @codingStandardsIgnoreStart
trait CTrait_Compat_Database_Driver_MongoDB_Result {
    /**
     * @param mixed $object
     * @param int   $type
     *
     * @deprecated use resultArray
     */
    public function as_array($object = null, $type = MYSQLI_ASSOC) {
        return $this->resultArray($object, $type);
    }

    /**
     * @param mixed $object
     * @param int   $type
     *
     * @deprecated use resultArray
     */
    public function result_array($object = null, $type = MYSQLI_ASSOC) {
        return $this->resultArray($object, $type);
    }

    /**
     * @deprecated use listFields
     */
    public function list_fields() {
        return $this->listFields();
    }
}
