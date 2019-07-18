<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2019, 3:18:29 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_ListGroup extends CAjax_Engine {

    public function execute() {
        $db = CDatabase::instance();
        $input = $this->input;
        $data = $this->ajaxMethod->getData();
        $owner = carr::get($data, 'owner');
        $owner = unserialize($owner);
        $app = CApp::instance();

        $tableData = $owner->getTableData();
        if (is_array($tableData)) {
            $index = 0;
            foreach ($tableData as $rowData) {
                $item = CElement_Factory::createComponent('ListGroup_Item');
                $item->setData($rowData)->setIndex($index);
                if ($owner->getItemCallback() != null) {
                    $item->setCallback($owner->getItemCallback(), $owner->getItemCallbackRequire());
                }
                $index++;
                $app->add($item);
            }
        }


        return $app->json();
    }

}
