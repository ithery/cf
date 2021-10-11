<?php

trait CAjax_Trait_DataTableTrait {
    public function getTable() {
        $data = $this->ajaxMethod->getData();
        $table = unserialize(carr::get($data, 'table'));
        return $table;
    }
}
