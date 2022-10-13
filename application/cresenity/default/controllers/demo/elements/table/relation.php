<?php

use Cresenity\Demo\Model\Category;

class Controller_Demo_Elements_Table_Relation extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Table Data From Model');

        // data show by children
        $app->addP()->add('Demo for table setDataFromModel with relation, data show by children');
        $table = $app->addTable();
        $table->setDataFromModel(Cresenity\Demo\Model\Item::class, function (CModel_Query $query) {
            $query->with('category');
        });
        $table->addColumn('name')->setLabel('Name');
        $table->addColumn('category.name')->setLabel('Category')->setWidth('200');
        $table->setAjax(false);

        $app->addH1()->add('&nbsp;'); // tricky use for space separator

        // data show by parent
        $app->addP()->add('Demo for table setDataFromModel with relation, data show by parent');
        $table2 = $app->addTable();
        $table2->setDataFromModel(Cresenity\Demo\Model\Category::class, function (CModel_Query $query) {
            $query->with('item');
        });
        $table2->addColumn('name')->setLabel('Name')->setWidth('200');
        $table2->addColumn('name')->setLabel('Items')->setCallback(function (Category $category, $value) {
            $items = c::collect($category->item)->pluck('name')->toArray();
            if (count($items) == 0) {
                return '-';
            }

            return implode(', ', $items);
        });
        $table2->setAjax(false);

        return $app;
    }
}
